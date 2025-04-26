<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Details</title>
    <style>
        .photos img {
            width: 100px;
            height: 100px;
            margin: 5px;
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .photos img:hover {
            transform: scale(1.1);
        }
    </style>
    <script>
        async function fetchPlaceDetails() {
            const placeName = document.getElementById('placeName').value;
            const response = await fetch(`google/getPlaceDetails?placeName=${encodeURIComponent(placeName)}`);
            const data = await response.json();

            const output = document.getElementById('output');
            if (data.error) {
                output.innerText = data.error;
                return;
            }

            output.innerHTML = `
                <h2>${data.name}</h2>
                <p><strong>Rating:</strong> ${data.rating} (${data.reviewsCount} reviews)</p>
                <p><a href="${data.businessLink}" target="_blank">View on Google Maps</a></p>
                <div class="photos">
                    ${data.photos.map(photo => `<img src="${photo}" alt="Photo">`).join('')}
                </div>
            `;
        }

        async function fetchSuggestions() {
            const input = document.getElementById('placeName').value;
            if (!input.trim()) return;

            const response = await fetch(`google/fetchPlaceSuggestions?input=${encodeURIComponent(input)}`);
            const data = await response.json();

            const suggestions = document.getElementById('suggestions');
            suggestions.innerHTML = '';

            data.forEach(suggestion => {
                const item = document.createElement('li');
                item.textContent = suggestion.description;
                item.onclick = () => {
                    document.getElementById('placeName').value = suggestion.description;
                    suggestions.innerHTML = '';
                };
                suggestions.appendChild(item);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Search Place Details</h1>
        <div>
            <input type="text" id="placeName" placeholder="Enter a place name" oninput="fetchSuggestions()">
            <button onclick="fetchPlaceDetails()">Get Details</button>
        </div>
        <ul id="suggestions" style="list-style: none; padding: 0; margin-top: 10px;"></ul>
        <div id="output" style="margin-top: 20px;"></div>
    </div>
</body>
</html>
