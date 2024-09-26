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

            <button type="submit" id="url-button"
              class="bg-blue-500 text-white px-4 py-2 sm:py-3 rounded-md hover:bg-blue-600 focus:outline-none w-full sm:w-40">
              Shorten URL
            </button>
          </div>
          <small id="original-url-error" style="color: red; display: none;">Please enter a valid URL</small>
        </form>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
          @if (!$urls->isEmpty())
            {{-- for showing success or error alert --}}
            @if (session("error") || session("success"))
              <div id="alert"
                class="flex items-center p-4 mb-4 text-sm {{ session("error") ? "text-red-800 border-red-300 bg-red-50" : "text-green-800 border-green-300 bg-green-50" }}"
                role="alert">

                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                  fill="currentColor" viewBox="0 0 20 20">
                  @if (session("error"))
                    <path
                      d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                  @elseif (session("success"))
                    <path d="M10 1a9 9 0 1 0 9 9A9.01 9.01 0 0 0 10 1ZM8 14l-3-3 1.41-1.41L8 11.17l5.59-5.59L15 7Z" />
                  @endif
                </svg>

                <span class="sr-only">{{ session("error") ? "Error" : "Success" }}</span>
                <div>{{ session("error") ? session("error") : session("success") }}</div>
              </div>
            @endif


            <table class="w-full text-sm text-left rtl:text-right text-gray-500 mt-1">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3">Serial</th>
                  <th scope="col" class="px-6 py-3">Original URL</th>
                  <th scope="col" class="px-6 py-3">Short URL</th>
                  <th scope="col" class="px-6 py-3">Clicked</th>
                  <th scope="col" class="px-6 py-3">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($urls as $url)
                  <tr class="odd:bg-white  even:bg-gray-50  border-b">
                    <td scope="row" class="px-6 py-4">{{ $loop->iteration }}</td> <!-- Incremental ID -->

                    <td scope="row" class="px-6 py-4 long-url" data-full-url="{{ $url->long_url }}">
                      {{ $url->long_url }}
                    </td>

                    <td scope="row" class="px-6 py-4">
                      <a href="{{ url("/go/" . $url->short_code) }}" target="_blank"
                        class="font-medium text-blue-600 hover:underline">
                        {{ url("/go/" . $url->short_code) }}
                      </a>
                    </td>

                    <td scope="row" class="px-6 py-4">{{ $url->click_count }}</td>

                    <td class="px-6 py-4 flex">
                      @auth
                        <a href="{{ route("short-url.insights", base64_encode($url->id)) }}" class="font-medium"
                          data-tippy-content="Show Insights">
                          <svg class="w-6 h-6 text-cyan-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                              d="M7.05 4.05A7 7 0 0 1 19 9c0 2.407-1.197 3.874-2.186 5.084l-.04.048C15.77 15.362 15 16.34 15 18a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1c0-1.612-.77-2.613-1.78-3.875l-.045-.056C6.193 12.842 5 11.352 5 9a7 7 0 0 1 2.05-4.95ZM9 21a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2h-4a1 1 0 0 1-1-1Zm1.586-13.414A2 2 0 0 1 12 7a1 1 0 1 0 0-2 4 4 0 0 0-4 4 1 1 0 0 0 2 0 2 2 0 0 1 .586-1.414Z"
                              clip-rule="evenodd" />
                          </svg>
                        </a> |
                      @endauth

                      <a href="#" class="font-medium"
                        onclick="copyToClipboard('{{ url("/go/" . $url->short_code) }}')"
                        data-tippy-content="Copy to clipboard">
                        <svg class="w-6 h-6 text-green-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                          width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                          <path fill-rule="evenodd"
                            d="M18 3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1V9a4 4 0 0 0-4-4h-3a1.99 1.99 0 0 0-1 .267V5a2 2 0 0 1 2-2h7Z"
                            clip-rule="evenodd" />
                          <path fill-rule="evenodd"
                            d="M8 7.054V11H4.2a2 2 0 0 1 .281-.432l2.46-2.87A2 2 0 0 1 8 7.054ZM10 7v4a2 2 0 0 1-2 2H4v6a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3Z"
                            clip-rule="evenodd" />
                        </svg>
                      </a> 

                      @auth
                      |
                        <a href="#" class="edit-link font-medium"
                          onclick="editUrl('{{ $url->long_url }}', '{{ base64_encode($url->id) }}')"
                          data-tippy-content="Edit Long URL">
                          <svg class="w-6 h-6 text-yellow-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                              d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z"
                              clip-rule="evenodd" />
                            <path fill-rule="evenodd"
                              d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z"
                              clip-rule="evenodd" />
                          </svg>
                        </a> |

                        <form method="post" action="{{ route("short-url.destroy", base64_encode($url->id)) }}"
                          style="display: inline-block;" data-tippy-content="Delete">
                          @csrf
                          @method("delete")
                          <button type="submit" class="font-medium"
                            onclick="return confirm('Are you sure you want to delete this?')">
                            <svg class="w-6 h-6  text-red-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                              width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                              <path fill-rule="evenodd"
                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                clip-rule="evenodd" />
                            </svg>
                          </button>
                        </form>
                      @endauth
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
            Detailed insights, manage your shortened links.
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
      // for form validation
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

      // for update any url
      function editUrl(longUrl, id) {
        document.getElementById('original-url').value = longUrl;
        document.getElementById('url-button').textContent = 'Update';
        // modify form action for updating the URL
        document.getElementById('url-form').action = `/short-url/${id}`;
      }

      // for auto hiding the alert
      document.addEventListener('DOMContentLoaded', function() {
        // check if the error alert is active
        var alert = document.getElementById('alert');
        if (alert) {
          setTimeout(function() {
            alert.style.opacity = '0'; // fade out effect
            setTimeout(function() {
              alert.remove();
            }, 300); // wait for the fade out transition
          }, 3000); // 3 seconds
        }
      });

      function copyToClipboard(url) {
        // create a temporary textarea element
        const tempInput = document.createElement('textarea');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select(); // select the textarea content
        document.execCommand('copy'); // copy the selected content
        document.body.removeChild(tempInput);
        alert('Short URL copied to clipboard!');
      }

      function truncateAndPopover(selector, dataAttr, charLimit) {
        // select all longUrlElements
        const longUrlElements = document.querySelectorAll(selector);

        longUrlElements.forEach(function(element) {
          // get the full text from the attribute
          const fullText = element.getAttribute('data-' + dataAttr);

          const wrapper = document.createElement('span');

          // check if the text exceeds the character limit
          if (fullText.length > charLimit) {
            const truncated = fullText.slice(0, charLimit) + '...';
            wrapper.innerHTML = '<span class="truncate">' + truncated + '</span>';
            element.innerHTML = ''; // clear the original content
            element.appendChild(wrapper);
          } else {
            wrapper.textContent = fullText;
            element.innerHTML = ''; // clear the original content
            element.appendChild(wrapper);
          }

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

      // call the function after the document is fully loaded
      document.addEventListener('DOMContentLoaded', function() {
        truncateAndPopover('.long-url', 'full-url', 22);
      });

      // initialize tippy tooltip
      document.addEventListener('DOMContentLoaded', function() {
        tippy('[data-tippy-content]');
      });
    </script>
  @endpush
</x-app-layout>
