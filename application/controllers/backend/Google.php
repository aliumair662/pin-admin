<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Google extends CI_Controller {

    const GOOGLE_API_KEY = 'AIzaSyDY9B2K8mOhMaB-LId-iXPw-YRvUowqJEE';

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->library('curl');
    }

    public function index() {
        $this->load->view('google_api_view');
    }

    private function fetchFromAPI($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getPlaceDetails() {
        $placeName = $this->input->get('placeName');
        if (!$placeName) {
            echo json_encode(['error' => 'Place name is required']);
            return;
        }

        $findPlaceURL = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=" . urlencode($placeName) . "&inputtype=textquery&fields=place_id&key=" . self::GOOGLE_API_KEY;
        $findPlaceResponse = $this->fetchFromAPI($findPlaceURL);

        if (empty($findPlaceResponse['candidates'][0]['place_id'])) {
            echo json_encode(['error' => 'Place not found']);
            return;
        }

        $placeId = $findPlaceResponse['candidates'][0]['place_id'];

        $placeDetailsURL = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$placeId&fields=name,rating,user_ratings_total,photos,url,geometry&key=" . self::GOOGLE_API_KEY;
        $placeDetailsResponse = $this->fetchFromAPI($placeDetailsURL);

        if (empty($placeDetailsResponse['result'])) {
            echo json_encode(['error' => 'Details not found']);
            return;
        }

        $details = $placeDetailsResponse['result'];
        $photos = [];

        if (!empty($details['photos'])) {
            foreach (array_slice($details['photos'], 0, 3) as $photo) {
                $photos[] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=" . $photo['photo_reference'] . "&key=" . self::GOOGLE_API_KEY;
                //$this->import_image_from_google($photo['photo_reference']);
            }
        }

        echo json_encode([
            'placeId' => $placeId,
            'name' => $details['name'],
            'rating' => $details['rating'],
            'reviewsCount' => $details['user_ratings_total'],
            'businessLink' => $details['url'],
            'geometry' => $details['geometry']['location'],
            'photos' => $photos,
        ]);
    }

    public function fetchPlaceSuggestions() {
        $query = $this->input->get('input');
        if (!$query) {
            echo json_encode(['error' => 'Query is required']);
            return;
        }

        $autocompleteURL = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=" . urlencode($query) . "&types=geocode&key=" . self::GOOGLE_API_KEY;
        $response = $this->fetchFromAPI($autocompleteURL);

        echo json_encode($response['predictions'] ?? []);
    }


    // Function to download and upload Google Place photo
    public function import_image_from_google($photo_reference) {
        $api_key = self::GOOGLE_API_KEY; // Replace with your actual Google API key
        $url = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photo_reference=$photo_reference&key=$api_key";

        // Get the image content from Google Places API
        $image_data = $this->curl->simple_get($url);

        if (!$image_data) {
            echo 'Failed to fetch image from Google Places.';
            return;
        }

        // Generate a unique filename for the image
        $filename = uniqid('google_place_', true) . '.jpg';
        $upload_path = './uploads/google/' . $filename;

        // Save the image to the server
        if (!write_file($upload_path, $image_data)) {
            echo 'Failed to save image to the server.';
            return;
        }

        echo "Image imported successfully! Saved as: " . $filename;
    }
}

?>