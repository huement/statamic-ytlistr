@extends('statamic::layout')
@section('title', 'YouTube Listr')

@section('content')
    {{--
        IMPORTANT: We use v-pre here to tell Vue.js to ignore this section.
        Since you are using standard Blade syntax, this prevents Vue from
        trying to "compile" your curly braces and crashing the page.
    --}}
    <div v-pre>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="mb-0">YouTube Listr</h1>
                @if($lastSync)
                    <p class="text-xs text-gray-500 mt-2">
                        Last synced: {{ $lastSync->diffForHumans() }}
                    </p>
                @endif
            </div>

            @if($isConfigured)
                <form method="POST" action="{{ cp_route('statamic-ytlistr.sync') }}">
                    @csrf
                    <button type="submit" class="btn-primary">
                        Sync Videos
                    </button>
                </form>
            @endif
        </div>

        @if(!$isConfigured)
            <div class="card p-4 mb-4 border-l-4 border-yellow-400 bg-yellow-50">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 font-bold">Configuration Required</p>
                        <p class="mt-1 text-sm text-yellow-700">Add these to your .env file:</p>
                        <pre class="mt-2 text-xs bg-gray-800 text-green-400 p-3 rounded">YOUTUBE_API_KEY=...
YOUTUBE_CHANNEL_ID=...</pre>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="card p-0 overflow-hidden">
            @if($videos->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Video</th>
                            <th>Published</th>
                            <th>Views</th>
                            <th>Duration</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($videos as $video)
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        <img src="{{ $video->thumbnail_url }}" class="h-10 w-16 object-cover rounded mr-3">
                                        <div>
                                            <a href="https://youtu.be/{{ $video->video_id }}" target="_blank" class="text-blue-600 hover:underline font-medium text-sm">
                                                {{ Str::limit($video->title, 50) }}
                                            </a>
                                            <div class="text-xs text-gray-500">{{ $video->video_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm">{{ $video->published_at->format('M j, Y') }}</td>
                                <td class="text-sm">{{ number_format($video->view_count) }}</td>
                                <td class="text-sm">{{ gmdate('i:s', $video->duration) }}</td>
                                <td class="text-right">
                                    <form method="POST" action="{{ cp_route('statamic-ytlistr.destroy', $video->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Remove this video?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($videos->hasPages())
                    <div class="p-3 border-t">
                        {{ $videos->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No videos found. Click Sync to get started.</p>
                </div>
            @endif
        </div>

        <div class="mt-8">
            <h2 class="font-bold mb-2">Template Cheat Sheet</h2>
            <div class="card p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs uppercase font-bold text-gray-500">Basic Loop</span>
                        <pre class="bg-gray-800 text-white p-2 rounded mt-1 text-xs">@{{ yt_listr limit="5" }}
  &lt;img src="@{{ thumbnail_url }}"&gt;
  &lt;h3&gt;@{{ title }}&lt;/h3&gt;
@{{ /yt_listr }}</pre>
                    </div>
                    <div>
                        <span class="text-xs uppercase font-bold text-gray-500">Latest Video</span>
                        <pre class="bg-gray-800 text-white p-2 rounded mt-1 text-xs">@{{ yt_listr:latest }}
  &lt;h2&gt;@{{ title }}&lt;/h2&gt;
  &lt;iframe src="@{{ embed_url }}"&gt;&lt;/iframe&gt;
@{{ /yt_listr:latest }}</pre>
                    </div>
                    <div>
                        <span class="text-xs uppercase font-bold text-gray-500">Video Count</span>
                        <pre class="bg-gray-800 text-white p-2 rounded mt-1 text-xs">Total videos: @{{ yt_listr:count }}</pre>
                    </div>
                    <div>
                        <span class="text-xs uppercase font-bold text-gray-500">Available Data Fields</span>
                        <div class="bg-gray-100 p-2 rounded mt-1 text-xs">
                            <div class="grid grid-cols-2 gap-1">
                                <code class="text-blue-600">title</code>
                                <code class="text-blue-600">video_id</code>
                                <code class="text-blue-600">description</code>
                                <code class="text-blue-600">thumbnail_url</code>
                                <code class="text-blue-600">published_at</code>
                                <code class="text-blue-600">duration</code>
                                <code class="text-blue-600">duration_formatted</code>
                                <code class="text-blue-600">view_count</code>
                                <code class="text-blue-600">like_count</code>
                                <code class="text-blue-600">comment_count</code>
                                <code class="text-blue-600">youtube_url</code>
                                <code class="text-blue-600">embed_url</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Info & Credits -->
        <div class="mt-8 border-t pt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Package Links -->
                <div class="card p-4">
                    <h3 class="font-bold text-sm mb-3 text-gray-700">Package Resources</h3>
                    <div class="space-y-2">
                        <a href="https://github.com/huement/statamic-ytlistr"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                            View on GitHub
                        </a>
                        <a href="https://packagist.org/packages/huement/statamic-ytlistr"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm6.441 13.441c-.586.586-1.536.586-2.121 0l-2.121-2.121c-.586-.586-.586-1.536 0-2.121l2.121-2.121c.586-.586 1.536-.586 2.121 0 .586.586.586 1.536 0 2.121l-1.06 1.061 1.06 1.06c.586.586.586 1.536 0 2.121zm-4.5 4.5c-.586.586-1.536.586-2.121 0L5.559 11.68c-.586-.586-.586-1.536 0-2.121l6.261-6.261c.586-.586 1.536-.586 2.121 0 .586.586.586 1.536 0 2.121L8.5 10.859l5.441 5.441c.586.586.586 1.536 0 2.121z"/>
                            </svg>
                            View on Packagist
                        </a>
                    </div>
                </div>

                <!-- Credits -->
                <div class="card p-4 bg-gradient-to-br from-blue-50 to-indigo-50">
                    <h3 class="font-bold text-sm mb-2 text-gray-700">Sponsored & Created By</h3>
                    <a href="https://huement.com"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-block">
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                            </svg>
                            <span class="text-lg font-bold text-blue-600 hover:text-blue-800">Huement.com</span>
                        </div>
                    </a>
                    <p class="text-xs text-gray-600 mt-2">
                        A software studio creating awesome content and exceptional web experiences.
                    </p>
                    <div class="mt-3 text-xs text-gray-500">
                        <span class="font-medium">License:</span> MIT •
                        <span class="font-medium">Version:</span> 1.0.0
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
