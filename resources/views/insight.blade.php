<x-app-layout>
  <div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-4xl p-4 sm:p-8 bg-white rounded-lg shadow-md">
      <div class="flex justify-between items-center">
        <div class="text-3xl font-semibold">Detail Insight</div>
        <a href="{{ route("short-url.index") }}"
          class="text-black-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-2 text-center me-2 mb-2">
          <svg class="w-5 h-5 text-gray-800 inline-block" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5 12h14M5 12l4-4m-4 4 4 4" />
          </svg>
          Back
        </a>
      </div>

      <hr class="h-px my-4 bg-gray-200 border-0">

      <div>
        Long URL: {{ $url_insight->long_url }} <br>
        Short URL: <a href="{{ url("/go/" . $url_insight->short_code) }}" target="_blank"
          class="font-medium text-blue-600 hover:underline">
          {{ url("/go/" . $url_insight->short_code) }}
        </a><br>
        Total Clicked: {{ $url_insight->click_count }}

        @if (!$url_insight->clickDetails->isEmpty())
          <table class="w-full text-sm text-left rtl:text-right text-gray-500 mt-1">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3">Serial</th>
                <th scope="col" class="px-6 py-3">IP Address</th>
                <th scope="col" class="px-6 py-3">Clicked At</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($url_insight->clickDetails as $clickDetail)
                <tr class="odd:bg-white even:bg-gray-50 border-b">
                  <td scope="row" class="px-6 py-4">{{ $loop->iteration }}</td> <!-- Incremental ID -->
                  <td scope="row" class="px-6 py-4">{{ $clickDetail->ip_address }}</td>
                  <td scope="row" class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($clickDetail->clicked_at)->format("d-m-Y H:i") }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <h1 class="text-center text-2xl text-red-600">No insights to show</h1>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
