<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortUrlRequest;
use App\Models\ClickDetail;
use App\Models\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $urls = collect();
        
        // ? Check if the user is logged in
        if (Auth::check()) {
            $urls = Url::where('user_id', Auth::user()->id)->get();
        } else {
            // * For non-logged-in users, fetch URLs using the visitor token from the session
            $visitorToken = Session::get('v-token');

            if ($visitorToken) {
                $urls = Url::where('visitor_token', base64_decode($visitorToken))->get();
            }
        }

        return view('index')->with("urls", $urls);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShortUrlRequest $request)
    {
        // ? server-side validation taken care in ShortUrlRequest
        $params = $request->all();

        do {
            $urlCode = Str::random(5);
            $hasCode = Url::where("short_code", $urlCode)->first();
        } while (!empty($hasCode)); // ! Keep generating until a unique code is found

        // * For non-logged-in users, check if visitor token exists in the session
        $visitorToken = Session::get('v-token');
        if (empty($visitorToken)) {
            do {
                $visitorToken = Str::random(5);
                $hasVisitor = Url::where("visitor_token", $visitorToken)->first();
            } while (!empty($hasVisitor)); // ! Keep generating until a unique token is found

            // ? should use strong encryption but now using it for example
            Session::put("v-token", base64_encode($visitorToken));
        } else {
            // ! Decode visitor token when fetching it from the session to store plain token
            $visitorToken = base64_decode($visitorToken);
        }

        Url::create([
            'user_id' => Auth::check() ? Auth::user()->id : null,
            'visitor_token' => Auth::check() ? null : $visitorToken,
            'long_url' => $params["original-url"],
            'short_code' => $urlCode
        ]);

        return redirect()->route('short-url.index')->with('success', 'URL added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShortUrlRequest $request, string $id)
    {
        // checking if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // ? should use strong encryption/decryption but now using it for example
        $id = base64_decode($id);
        $url = Url::findOrFail($id);
        $url->long_url = $request->input('original-url');
        $url->save();

        return redirect()->route('short-url.index')->with('success', 'URL updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // checking if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $id = base64_decode($id);
        Url::where("id", $id)->delete();

        return redirect()->route('short-url.index')->with('success', 'URL deleted successfully!');
    }

    public function redirectUrl($short_code) {
        $url = Url::where('short_code', $short_code)->first();
        if ($url) {
            // save the click count
            $url->click_count = ++$url->click_count;
            $url->save();

            // log the click details
            ClickDetail::insert([
                'url_id'     => $url->id,
                'ip_address' => FacadesRequest::ip(), // get the user's IP address
            ]);

            // redirect to the long URL
            return redirect()->away($url->long_url);
        }

        return redirect()->route('short-url.index')->with('error', 'Short URL not found.');
    }

    public function urlInsights($id) {
        // checking if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $id = base64_decode($id);

        $urls = Url::where("id", $id)->with('clickDetails')->first();
        if (!$urls) {
            return redirect()->route('short-url.index');
        }

        return view('insight')->with('url_insight', $urls);
    }

    public function getShortUrl($url) {
        // Validate the incoming URL
        $validator = Validator::make(['url' => $url], [
            'url' => 'required|url' // Ensure the URL is a valid format
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid URL provided!',
                'errors' => $validator->errors()
            ], 422);
        }

        do {
            $urlCode = Str::random(5);
            $hasCode = Url::where("short_code", $urlCode)->first();
        } while (!empty($hasCode)); // ! Keep generating until a unique code is found

        // * For non-logged-in users, check if visitor token exists in the session
        $visitorToken = Session::get('v-token');
        if (empty($visitorToken)) {
            do {
                $visitorToken = Str::random(5);
                $hasVisitor = Url::where("visitor_token", $visitorToken)->first();
            } while (!empty($hasVisitor)); // ! Keep generating until a unique token is found

            // ? should use strong encryption but now using it for example
            Session::put("v-token", base64_encode($visitorToken));
        } else {
            // ! Decode visitor token when fetching it from the session to store plain token
            $visitorToken = base64_decode($visitorToken);
        }

        // ! checking for spam protection
        $hasUrl = Url::where("visitor_token", $visitorToken)->where("long_url", $url)->first();

        if (!$hasUrl) {
            $inserted = Url::create([
                'visitor_token' => $visitorToken,
                'long_url' => $url,
                'short_code' => $urlCode
            ]);

            return response()->json([
                'success' => true,
                'message' => 'URL shortened successfully!',
                'data' => [
                    'short_url' => url("/{$inserted->short_code}"),
                    'long_url' => $inserted->long_url,
                ]
            ], 201);
        }

        return response()->json([
            'success' => true,
            'message' => 'URL shortened successfully!',
            'data' => [
                'short_url' => url("/go/{$hasUrl->short_code}"),
                'long_url' => $hasUrl->long_url,
            ]
        ], 201);
    }
}
