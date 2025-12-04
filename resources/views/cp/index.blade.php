<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Listr - Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">YouTube Listr</h1>
                        @if($lastSync)
                            <p class="text-sm text-gray-500 mt-1">Last synced: {{ $lastSync->diffForHumans() }}</p>
                        @endif
                    </div>
                    @if($isConfigured)
                        <form method="POST" action="{{ cp_route('ytlistr.sync') }}">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow">
                                Sync Videos from YouTube
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Config Warning -->
            @if(!$isConfigured)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 font-semibold">Configuration Required</p>
                            <p class="mt-1 text-sm text-yellow-700">Add these to your <code class="bg-yellow-100 px-1 rounded">.env</code> file:</p>
                            <pre class="mt-2 text-xs bg-gray-800 text-green-400 p-3 rounded">YOUTUBE_API_KEY=your_api_key_here
YOUTUBE_CHANNEL_ID=your_channel_id_here</pre>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Videos Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if($videos->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Video</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($videos as $video)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="h-12 w-20 object-cover rounded">
                                            <div class="ml-4">
                                                <a href="{{ $video->youtube_url }}" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                    {{ Str::limit($video->title, 60) }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $video->video_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $video->published_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($video->view_count) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ gmdate('i:s', $video->duration) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <form method="POST" action="{{ cp_route('ytlistr.destroy', $video->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this video?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($videos->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200">
                            {{ $videos->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No videos</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by syncing videos from YouTube.</p>
                    </div>
                @endif
            </div>

            <!-- Usage Examples -->
            <div class="bg-white shadow rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Usage in Templates</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">List videos:</p>
                        <pre class="bg-gray-800 text-green-400 p-3 rounded text-xs overflow-x-auto">&#123;&#123; yt_listr limit="5" &#125;&#125;
    &lt;h3&gt;&#123;&#123; title &#125;&#125;&lt;/h3&gt;
    &lt;a href="&#123;&#123; youtube_url &#125;&#125;"&gt;Watch&lt;/a&gt;
&#123;&#123; /yt_listr &#125;&#125;</pre>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Get latest video:</p>
                        <pre class="bg-gray-800 text-green-400 p-3 rounded text-xs overflow-x-auto">&#123;&#123; yt_listr:latest &#125;&#125;
    &lt;h2&gt;&#123;&#123; title &#125;&#125;&lt;/h2&gt;
&#123;&#123; /yt_listr:latest &#125;&#125;</pre>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Video count:</p>
                        <pre class="bg-gray-800 text-green-400 p-3 rounded text-xs overflow-x-auto">Total: &#123;&#123; yt_listr:count &#125;&#125;</pre>
                    </div>
                </div>
            </div>

            <!-- Back to CP Link -->
            <div class="mt-6 text-center">
                <a href="{{ cp_route('index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Back to Control Panel</a>
            </div>
        </div>
    </div>
</body>
</html>
