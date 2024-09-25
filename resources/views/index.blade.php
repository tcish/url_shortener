<x-app-layout>
  <div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-4xl p-4 sm:p-8 bg-white rounded-lg shadow-md">
      <!-- Top Section -->
      <div>
        <h2 class="text-xl sm:text-2xl font-bold mb-4 text-center">Paste the URL to be shortened</h2>
        <form id="url-form" action="{{ route("short-url.store") }}" method="POST">
          @csrf
          <div class="flex flex-col sm:flex-row items-center sm:space-x-2 space-y-2 sm:space-y-0">
            <input type="text" id="original-url" name="original-url" placeholder="Enter the link here"
              class="w-full px-4 py-2 sm:py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit"
              class="bg-blue-500 text-white px-4 py-2 sm:py-3 rounded-md hover:bg-blue-600 focus:outline-none w-full sm:w-40">
              Shorten URL
            </button>
          </div>
          <small id="original-url-error" style="color: red; display: none;">Please enter a valid URL</small>
        </form>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
          @if (!$urls->isEmpty())
            {{-- for showing invalid short url alert --}}
            @if (session("error"))
              <div id="error-alert"
                class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800"
                role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div>{{ session("error") }}</div>
              </div>
            @endif

            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-1">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                  <th scope="col" class="px-6 py-3">Serial</th>
                  <th scope="col" class="px-6 py-3">Original URL</th>
                  <th scope="col" class="px-6 py-3">Short URL</th>
                  <th scope="col" class="px-6 py-3">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($urls as $url)
                  <tr
                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4">{{ $loop->iteration }}</th> <!-- Incremental ID -->
                    <th scope="row" class="px-6 py-4 long-url" data-full-url="{{ $url->long_url }}">
                      {{ $url->long_url }}
                    </th>
                    <th scope="row" class="px-6 py-4">
                      <a href="{{ url("/go/" . $url->short_code) }}" target="_blank"
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        {{ url("/go/" . $url->short_code) }}
                      </a>
                    </th>
                    <td class="px-6 py-4">
                      <a href="#" class="font-medium text-green-600 dark:text-green-500 hover:underline"
                        onclick="copyToClipboard('{{ url("/go/" . $url->short_code) }}')">Copy</a> |
                      <a href="#"
                        class="font-medium text-yellow-600 dark:text-yellow-500 hover:underline">Edit</a> |
                      <a href="#" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>

      @guest
        <!-- Bottom Section -->
        <div class="mt-8 bg-gray-50 p-4 sm:p-6 rounded-lg">
          <h3 class="text-lg sm:text-xl font-semibold text-center mb-4">Want More?</h3>
          <p class="text-center text-gray-500 mb-4">
            Detailed analytics, manage your shortened links.
          </p>
          <div class="flex justify-center">
            <a href="{{ route("register") }}"
              class="bg-blue-500 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-600 focus:outline-none">
              Create Account
            </a>
          </div>
        </div>
      @endguest
    </div>
  </div>

  @push("scripts")
    <script>
      const form = document.getElementById('url-form');
      const input = document.getElementById('original-url');
      const errorElement = document.getElementById('original-url-error');

      form.addEventListener('submit', function(e) {
        const urlPattern =
          /^(https?:\/\/(localhost|127\.0\.0\.1)(:\d{1,5})?(\/[^\s]*)?|https?:\/\/[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\.[a-zA-Z]{2,})?(\/[^\s]*)?)$/;
        const inputValue = input.value.trim();

        if (!urlPattern.test(inputValue)) {
          e.preventDefault(); // Prevent form submission
          input.style.border = '2px solid red'; // Apply red border
          errorElement.style.display = 'block'; // Show error message
        } else {
          input.style.border = ''; // Remove red border
          errorElement.style.display = 'none'; // Hide error message
        }
      });

      // for auto hiding the alert
      document.addEventListener('DOMContentLoaded', function() {
        // Check if the error alert is active
        var alert = document.getElementById('error-alert');
        if (alert) {
          setTimeout(function() {
            alert.style.opacity = '0'; // Fade out effect
            setTimeout(function() {
              alert.remove();
            }, 300); // Wait for the fade out transition
          }, 3000); // 3 seconds
        }
      });

      function copyToClipboard(url) {
        // Create a temporary textarea element
        const tempInput = document.createElement('textarea');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select(); // Select the textarea content
        document.execCommand('copy'); // Copy the selected content
        document.body.removeChild(tempInput);
        alert('Short URL copied to clipboard!');
      }

      function truncateAndPopover(selector, dataAttr, charLimit) {
        // Select all longUrlElements
        const longUrlElements = document.querySelectorAll(selector);

        longUrlElements.forEach(function(element) {
          // Get the full text from the attribute
          const fullText = element.getAttribute('data-' + dataAttr);

          const wrapper = document.createElement('span');

          // Check if the text exceeds the character limit
          if (fullText.length > charLimit) {
            const truncated = fullText.slice(0, charLimit) + '...';
            wrapper.innerHTML = '<span class="truncate">' + truncated + '</span>';
            element.innerHTML = ''; // Clear the original content
            element.appendChild(wrapper);
          } else {
            wrapper.textContent = fullText;
            element.innerHTML = ''; // Clear the original content
            element.appendChild(wrapper);
          }

          // Initialize Tippy.js for the tooltip
          tippy(wrapper, {
            content: fullText,
            allowHTML: true,
            interactive: true,
            delay: [200, 0],
            placement: 'top',
            appendTo: document.body,
          });
        });
      }

      // Call the function after the document is fully loaded
      document.addEventListener('DOMContentLoaded', function() {
        truncateAndPopover('.long-url', 'full-url', 36);
      });
    </script>
  @endpush
</x-app-layout>
