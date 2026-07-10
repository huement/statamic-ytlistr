@extends('statamic::layout')
@section('title', 'YouTube Listr')

@section('content')
    @php
        // Server-side summary calculations for our analytics grid
        $totalVideos = $videos->count();
        $totalViews = $videos->sum('view_count');
    @endphp

    <div
        v-pre
        class="-m-6 min-h-screen w-full space-y-6 p-6"
        style="
            background-color: #18181b;
            color: #d4d4d8;
            font-family: ui-monospace, monospace;
            letter-spacing: -0.01em;
            box-sizing: border-box;
        "
    >
        {{-- Header Panel --}}
        <header
            class="rounded-2xl border border-zinc-800/80 p-6 shadow-2xl"
            style="
                background-color: rgba(14, 14, 17, 0.4);
                border: 1px solid #1f1f23;
            "
        >
            <div
                class="flex w-full flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <div class="flex items-center space-x-4">
                    <div>
                        <h1
                            class="mb-0 text-xl font-bold tracking-tight text-white"
                        >
                            YouTube Listr
                        </h1>
                        @if ($lastSync)
                            <p class="mt-1 text-xs text-zinc-500">
                                // SYSTEM_LAST_SYNC:
                                {{ $lastSync->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    @if ($isConfigured)
                        <form
                            method="POST"
                            action="{{ cp_route('statamic-ytlistr.sync') }}"
                            class="m-0 p-0"
                        >
                            @csrf
                            {{-- FIXED: Added explicit interactive hover & cursor pointer behaviors --}}
                            <button
                                type="submit"
                                class="cursor-pointer rounded-xl bg-cyan-600 px-4 py-2 text-xs font-bold tracking-wide text-white shadow-lg shadow-cyan-900/20 transition-all duration-150 hover:bg-cyan-500 active:scale-98"
                            >
                                Sync Live Feed
                            </button>
                        </form>
                    @endif

                    <a
                        href="https://huement.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex items-center space-x-2 rounded-lg border border-zinc-800 bg-zinc-900 px-3 py-1.5 text-xs font-bold text-zinc-300 transition-all hover:bg-zinc-800 hover:text-cyan-400"
                    >
                        <span>HUEMENT</span>
                        <svg
                            class="h-3.5 w-3.5 text-zinc-500 transition-colors group-hover:text-cyan-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"
                            />
                        </svg>
                    </a>
                </div>
            </div>
        </header>

        {{-- System Response Alerts --}}
        @if (session('success'))
            <div
                class="flex items-center space-x-3 rounded-xl border border-emerald-500/30 bg-zinc-900 p-4 text-xs text-zinc-200 shadow-xl"
            >
                <span
                    class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"
                    style="height: 10px; width: 10px"
                ></span>
                <span>// SUCCESS_PROTOCOL: {{ session('success') }}</span>
            </div>
        @endif

        @if (! $isConfigured)
            <div
                class="space-y-2 rounded-xl border border-amber-500/30 bg-zinc-900 p-4 text-xs text-zinc-200 shadow-xl"
            >
                <div
                    class="flex items-center space-x-2 font-bold tracking-wider text-amber-400 uppercase"
                >
                    <span
                        class="h-2 w-2 animate-pulse rounded-full bg-amber-400"
                    ></span>
                    <span>Configuration Matrix Required</span>
                </div>
                <p class="text-zinc-400">
                    Append your API payloads to your environment configuration:
                </p>
                <pre
                    class="rounded-lg border border-zinc-800 bg-black/40 p-3 font-mono text-[11px] text-emerald-400"
                >
YOUTUBE_API_KEY=your_key_here&#10;YOUTUBE_CHANNEL_ID=your_channel_id_here</pre
                >
            </div>
        @endif

        {{-- Metric Analytics Grid --}}
        <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-3">
            {{-- Card 1: Total Indexes --}}
            <div
                style="
                    background-color: rgba(14, 14, 17, 0.4);
                    border: 1px solid #1f1f23;
                    border-radius: 12px;
                    padding: 24px;
                "
                onmouseover="this.style.borderColor = '#3f3f46'"
                onmouseout="this.style.borderColor = '#1f1f23'"
                class="shadow-xl transition-all duration-200"
            >
                <div class="mb-4 flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded bg-cyan-500/10 text-cyan-400"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold tracking-wider text-zinc-500 uppercase"
                    >
                        Video Index
                    </span>
                </div>
                <p
                    class="mb-1 text-[11px] font-bold tracking-wide text-zinc-500 uppercase"
                >
                    Total Stored
                </p>
                <div class="flex items-baseline gap-2">
                    <span
                        class="text-4xl font-extrabold tracking-tight text-cyan-400"
                    >
                        {{ $totalVideos }}
                    </span>
                    <span class="text-xs text-zinc-600">records</span>
                </div>
            </div>

            {{-- Card 2: Cumulative Engagement --}}
            <div
                style="
                    background-color: rgba(14, 14, 17, 0.4);
                    border: 1px solid #1f1f23;
                    border-radius: 12px;
                    padding: 24px;
                "
                onmouseover="this.style.borderColor = '#3f3f46'"
                onmouseout="this.style.borderColor = '#1f1f23'"
                class="shadow-xl transition-all duration-200"
            >
                <div class="mb-4 flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded bg-blue-500/10 text-blue-400"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold tracking-wider text-zinc-500 uppercase"
                    >
                        Engagement
                    </span>
                </div>
                <p
                    class="mb-1 text-[11px] font-bold tracking-wide text-zinc-500 uppercase"
                >
                    Cumulative Views
                </p>
                <div class="flex items-baseline gap-2">
                    <span
                        class="text-4xl font-extrabold tracking-tight text-blue-400"
                    >
                        {{ number_format($totalViews) }}
                    </span>
                    <span class="text-xs text-zinc-600">plays</span>
                </div>
            </div>

            {{-- Card 3: Synchronization Status --}}
            <div
                style="
                    background-color: rgba(14, 14, 17, 0.4);
                    border: 1px solid #1f1f23;
                    border-radius: 12px;
                    padding: 24px;
                "
                onmouseover="this.style.borderColor = '#3f3f46'"
                onmouseout="this.style.borderColor = '#1f1f23'"
                class="shadow-xl transition-all duration-200"
            >
                <div class="mb-4 flex items-center gap-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded bg-emerald-500/10 text-emerald-400"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 11H18"
                            />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold tracking-wider text-zinc-500 uppercase"
                    >
                        Cron Registry
                    </span>
                </div>
                <p
                    class="mb-1 text-[11px] font-bold tracking-wide text-zinc-500 uppercase"
                >
                    Automation Loop
                </p>
                <div class="flex items-baseline gap-2">
                    <span
                        class="text-2xl font-extrabold tracking-tight text-emerald-400"
                    >
                        {{ config('statamic-ytlistr.scheduler.enabled', true) ? 'ACTIVE' : 'STANDBY' }}
                    </span>
                    <span class="text-xs text-zinc-600">
                        //
                        {{ config('statamic-ytlistr.scheduler.frequency', 'daily') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Main Table Area Container Canvas --}}
        <div
            class="overflow-hidden shadow-2xl"
            style="
                background-color: rgba(14, 14, 17, 0.4);
                border: 1px solid #1f1f23;
                border-radius: 1rem;
            "
        >
            <div
                class="flex items-center space-x-3 border-b border-zinc-800 bg-zinc-950/40 p-5"
            >
                <span
                    class="h-2 w-2 animate-pulse rounded-full bg-cyan-400"
                ></span>
                <span
                    class="text-xs font-bold tracking-wider text-zinc-300 uppercase"
                >
                    Active Repository Video Trails
                </span>
            </div>

            @if ($videos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-zinc-800/80">
                                <th
                                    class="px-6 py-4 text-[11px] font-bold tracking-wider text-zinc-500 uppercase"
                                >
                                    Video Payload Details
                                </th>
                                <th
                                    class="px-4 py-4 text-[11px] font-bold tracking-wider text-zinc-500 uppercase"
                                >
                                    Published
                                </th>
                                <th
                                    class="px-4 py-4 text-[11px] font-bold tracking-wider text-zinc-500 uppercase"
                                >
                                    View Count
                                </th>
                                <th
                                    class="px-4 py-4 text-[11px] font-bold tracking-wider text-zinc-500 uppercase"
                                >
                                    Duration
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-[11px] font-bold tracking-wider text-zinc-500 uppercase"
                                >
                                    Mod_Controls
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-zinc-800/40 text-xs text-zinc-300"
                        >
                            @foreach ($videos as $video)
                                <tr
                                    class="group transition-all duration-150 hover:bg-zinc-950/60"
                                    style="{{ $loop->even ? 'background-color: rgba(10,10,10,0.15)' : 'background-color: rgba(30,30,30,0.15)' }}"
                                >
                                    <td class="px-6 py-4">
                                        <div
                                            class="flex items-center space-x-4"
                                        >
                                            <div class="relative shrink-0">
                                                <img
                                                    src="{{ $video->thumbnail_url }}"
                                                    class="h-10 w-16 rounded-lg border border-zinc-800 object-cover shadow"
                                                />
                                                <div
                                                    class="pointer-events-none absolute inset-0 rounded-lg shadow-inner"
                                                ></div>
                                            </div>
                                            <div class="min-w-0">
                                                <a
                                                    href="https://youtu.be/{{ $video->video_id }}"
                                                    target="_blank"
                                                    class="block max-w-md truncate font-mono text-xs font-medium text-zinc-200 transition-colors hover:text-cyan-400"
                                                >
                                                    {{ Str::limit($video->title, 60) }}
                                                </a>
                                                <div
                                                    class="mt-0.5 font-mono text-[10px] text-zinc-600"
                                                >
                                                    {{ $video->video_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 font-mono whitespace-nowrap text-zinc-400"
                                    >
                                        {{ $video->published_at->format('Y-m-d') }}
                                    </td>
                                    <td
                                        class="px-4 py-4 font-mono whitespace-nowrap text-zinc-400"
                                    >
                                        {{ number_format($video->view_count) }}
                                    </td>
                                    <td
                                        class="px-4 py-4 font-mono whitespace-nowrap text-zinc-400"
                                    >
                                        {{ gmdate('i:s', $video->duration) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right whitespace-nowrap"
                                    >
                                        {{-- Row tools fade in smoothly on hover like the comments plugin --}}
                                        <div
                                            class="inline-flex items-center justify-end space-x-2 opacity-30 transition-all duration-150 group-hover:opacity-100"
                                        >
                                            <form
                                                method="POST"
                                                action="{{ cp_route('statamic-ytlistr.destroy', $video->id) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'WARNING: Purge this media packet trace?'
                                                    );
                                                "
                                                class="m-0 p-0"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    title="Scrub trace packet"
                                                    class="cursor-pointer rounded-lg border border-zinc-800 bg-zinc-900 p-1.5 text-red-400 transition-all hover:border-red-500/40 hover:bg-red-500/10"
                                                >
                                                    <svg
                                                        class="h-3.5 w-3.5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($videos->hasPages())
                        <div
                            class="border-t border-zinc-800 bg-zinc-950/20 p-4"
                        >
                            {{ $videos->links() }}
                        </div>
                    @endif
                </div>
            @else
                <div
                    class="flex flex-col items-center justify-center p-24 text-center"
                >
                    <svg
                        class="mb-3 h-8 w-8 animate-pulse text-cyan-500/30"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                        />
                    </svg>
                    <p
                        class="text-xs font-bold tracking-widest text-zinc-400 uppercase"
                    >
                        // NO_ACTIVE_DATA_TRAILS_FOUND
                    </p>
                    <p class="mt-1 text-[11px] text-zinc-600">
                        Click "Sync Live Feed" above to stream arrays into your
                        environment database.
                    </p>
                </div>
            @endif
        </div>

        {{-- Template Reference Cards --}}
        <div class="mt-8 space-y-3">
            <h2
                class="text-xs font-bold tracking-wider text-zinc-400 uppercase"
            >
                // Front-End Template Integration Matrix
            </h2>
            <div
                class="space-y-6 rounded-xl border border-zinc-800/80 p-6 shadow-xl"
                style="background-color: rgba(14, 14, 17, 0.4)"
            >
                {{-- Row 1: Native Statamic Antlers Context --}}
                <div>
                    <span
                        class="mb-2 block font-mono text-[11px] font-bold tracking-wide text-cyan-500 uppercase"
                    >
                        [01] Native Antlers Engine Context
                    </span>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <span
                                class="font-mono text-[10px] font-bold text-zinc-600 uppercase"
                            >
                                Collection Loop
                            </span>
                            <pre
                                class="mt-1 rounded-lg border border-zinc-800/60 bg-black/40 p-3 font-mono text-xs text-zinc-300"
                            >
&#64;&#123;&#123; yt_listr limit="5" &#125;&#125;&#10;  &lt;img src="&#64;&#123;&#123; thumbnail_url &#125;&#125;"&gt;&#10;  &lt;h3&gt;&#64;&#123;&#123; title &#125;&#125;&lt;/h3&gt;&#10;&#64;&#123;&#123; /yt_listr &#125;&#125;</pre
                            >
                        </div>
                        <div>
                            <span
                                class="font-mono text-[10px] font-bold text-zinc-600 uppercase"
                            >
                                Isolated Headliner
                            </span>
                            <pre
                                class="mt-1 rounded-lg border border-zinc-800/60 bg-black/40 p-3 font-mono text-xs text-zinc-300"
                            >
&#64;&#123;&#123; yt_listr:latest &#125;&#125;&#10;  &lt;h2&gt;&#64;&#123;&#123; title &#125;&#125;&#125;&#125;&lt;/h2&gt;&#10;  &lt;iframe src="&#64;&#123;&#123; embed_url &#125;&#125;"&gt;&lt;/iframe&gt;&#10;&#64;&#123;&#123; /yt_listr:latest &#125;&#125;</pre
                            >
                        </div>
                    </div>
                </div>

                <div class="my-4 border-t border-zinc-800/60"></div>

                {{-- Row 2: Laravel Blade Context (Both Approaches) --}}
                <div>
                    <span
                        class="mb-2 block font-mono text-[11px] font-bold tracking-wide text-emerald-500 uppercase"
                    >
                        [02] Laravel Blade Engine Context (Driver-Agnostic)
                    </span>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {{-- Approach A: Statamic Tag Facade --}}
                        <div>
                            <span
                                class="font-mono text-[10px] font-bold text-zinc-600 uppercase"
                            >
                                Approach A: Statamic Tag Facade Wrapper
                            </span>
                            <p class="my-1 text-[11px] text-zinc-500">
                                Executes your custom Antlers tag parsing
                                directly inside standard Blade layouts:
                            </p>
                            <pre
                                class="mt-1 rounded-lg border border-zinc-800/60 bg-black/40 p-3 font-mono text-xs text-zinc-300"
                            >
&#123;&#123;!! Statamic::tag('yt_listr:latest')->render() !!&#125;&#125;</pre
                            >
                        </div>

                        {{-- Approach B: Service Container Repository Dependency --}}
                        <div>
                            <span
                                class="font-mono text-[10px] font-bold text-zinc-600 uppercase"
                            >
                                Approach B: Service Container Contract Binding
                            </span>
                            <p class="my-1 text-[11px] text-zinc-500">
                                Resolves the active storage driver (DB or YAML)
                                directly from the application runtime:
                            </p>
                            <pre
                                class="mt-1 rounded-lg border border-zinc-800/60 bg-black/40 p-3 font-mono text-xs text-zinc-300"
                            >
&#64;php&#10;  $repo = app(\Huement\StatamicYtListr\Repositories\VideoRepository::class);&#10;  $latestVideo = $repo->latest();&#10;&#64;endphp&#10;&#10;&#64;if($latestVideo)&#10;  {{-- Works dynamically with Eloquent models or fluent array maps --}}&#10;  &lt;h2&gt;&#123;&#123; data_get($latestVideo, 'title') &#125;&#125;&lt;/h2&gt;&#10;&#64;endif</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- /end Template Reference Cards --}}
    </div>
@endsection
