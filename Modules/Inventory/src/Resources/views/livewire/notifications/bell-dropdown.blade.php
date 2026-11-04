<div
    class="relative"
    wire:poll.10s="checkForNewNotifications"
    x-data="{
        audio: null,
        audioUnlocked: false,
        notificationHandler: null,

        init() {
            this.audio = new Audio('/sounds/notification-chime.wav');
            this.audio.preload = 'auto';
            this.audio.volume = 0.7;

            const unlockAudio = async () => {
                if (this.audioUnlocked) return;

                try {
                    this.audio.muted = true;
                    await this.audio.play();
                    this.audio.pause();
                    this.audio.currentTime = 0;
                    this.audio.muted = false;

                    this.audioUnlocked = true;
                    document.removeEventListener('click', unlockAudio);

                    console.log('Notification audio unlocked');
                } catch (error) {
                    console.error('Audio unlock failed:', error);
                }
            };

            document.addEventListener('click', unlockAudio);

            this.notificationHandler = () => {
                console.log('NEW NOTIFICATION EVENT RECEIVED');

                if (!this.audioUnlocked || !this.audio) {
                    console.log('Audio is not unlocked yet.');
                    return;
            }

            this.audio.currentTime = 0;

            this.audio.play().catch(error => {
                console.error('Notification sound failed:', error);
            });
        };

            window.addEventListener(
                'new-notification',
                this.notificationHandler
            );
        },

        destroy() {
            if (this.notificationHandler) {
                window.removeEventListener(
                    'new-notification',
                    this.notificationHandler
                );
            }

            if (this.audio) {
                this.audio.pause();
                this.audio = null;
            }
        }
    }"
    x-on:click.outside="$wire.close()">

    {{-- Bell Button --}}
    <button
        type="button"
        wire:click="toggle"
        aria-label="Toggle notifications"
        aria-expanded="{{ $open ? 'true' : 'false' }}"
        class="relative flex h-9 w-9 shrink-0 items-center justify-center overflow-visible rounded-full bg-teal-600 text-white transition hover:bg-teal-700"
        style="position: relative;">

        <svg
            class="block h-5 w-5"
            style="width: 20px; height: 20px; flex-shrink: 0;"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-4-5.7V5a2 2 0 0 0-4 0v.3A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" />
        </svg>

        {{-- Notification Badge --}}
        @if ($unreadCount > 0)
        <span
            data-unread-count
            style="
                    position: absolute !important;
                    top: -6px !important;
                    right: -6px !important;
                    z-index: 999 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    min-width: 19px;
                    height: 19px;
                    padding: 0 4px;
                    border: 2px solid white;
                    border-radius: 9999px;
                    background-color: #ef4444;
                    color: white;
                    font-size: 10px;
                    font-weight: 700;
                    line-height: 1;
                    pointer-events: none;
                ">
            {{ $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Notification Dropdown --}}
    @if ($open)
    <div
        class="absolute right-0 top-full z-[9999] mt-3 w-80 max-w-[calc(100vw-2rem)] origin-top-right sm:w-96">

        <div
            class="flex max-h-[min(24rem,calc(100vh-6rem))] flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl">

            {{-- Header --}}
            <div
                class="flex shrink-0 items-center justify-between gap-2 border-b border-gray-100 px-5 py-4">

                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900">
                        Notifications
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        {{ $unreadCount }} New
                    </p>
                </div>

                @if ($unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="shrink-0 text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    Mark all as read
                </button>
                @endif
            </div>

            {{-- Notification List --}}
            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden">

                @forelse ($notifications as $notification)

                @php
                $data = $notification->data;
                $isUnread = is_null($notification->read_at);
                $icon = $data['icon'] ?? 'bell';
                @endphp

                <div
                    wire:key="notification-{{ $notification->id }}"
                    class="relative flex gap-3 border-b border-gray-100 px-5 py-4 transition-colors duration-200
                                {{ $isUnread
                                    ? 'border-l-4 border-l-indigo-500 bg-indigo-50 hover:bg-indigo-100'
                                    : 'border-l-4 border-l-transparent bg-white hover:bg-gray-50'
                                }}">

                    {{-- Notification Icon --}}
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                                    {{ $isUnread
                                        ? 'bg-indigo-100 text-indigo-600'
                                        : 'bg-gray-100 text-gray-500'
                                    }}">

                        @if ($icon === 'alert-triangle')
                        ⚠
                        @elseif ($icon === 'truck')
                        🚚
                        @elseif ($icon === 'package-plus' || $icon === 'package')
                        📦
                        @elseif ($icon === 'package-minus')
                        📤
                        @elseif ($icon === 'sliders-horizontal')
                        ⚙
                        @elseif ($icon === 'shopping-cart')
                        🛒
                        @elseif ($icon === 'folder-plus')
                        🗂️
                        @else
                        🔔
                        @endif
                    </div>

                    {{-- Notification Content --}}
                    <div class="min-w-0 flex-1">

                        <div class="flex items-start justify-between gap-2">

                            <h4
                                class="truncate text-sm
                                            {{ $isUnread
                                                ? 'font-bold text-gray-900'
                                                : 'font-medium text-gray-700'
                                            }}">
                                {{ $data['title'] ?? 'Notification' }}
                            </h4>

                            {{-- Unread Indicator --}}
                            @if ($isUnread)
                            <span
                                class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-600 ring-2 ring-indigo-200"
                                title="Unread notification"></span>
                            @endif

                        </div>

                        <p
                            class="mt-1 break-words text-sm leading-5
                                        {{ $isUnread ? 'text-gray-700' : 'text-gray-500' }}">
                            {{ $data['message'] ?? '' }}
                        </p>

                        <div class="mt-2 flex items-center justify-between gap-2">

                            <span class="text-xs text-gray-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>

                            @if ($isUnread)
                            <button
                                type="button"
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                Mark as read
                            </button>
                            @endif

                        </div>

                    </div>
                </div>

                @empty

                <div class="px-5 py-12 text-center">

                    <div class="mb-3 text-3xl">🔔</div>

                    <h4 class="text-sm font-semibold text-gray-800">
                        You're all caught up!
                    </h4>

                    <p class="mt-1 text-xs text-gray-500">
                        New notifications will appear here.
                    </p>

                </div>

                @endforelse

            </div>

            {{-- Footer --}}
            <div
                class="shrink-0 border-t border-gray-100 bg-gray-50 px-5 py-3 text-center">

                <span class="text-xs text-gray-500">
                    Showing your latest {{ $notifications->count() }} notifications
                </span>

            </div>

        </div>
    </div>
    @endif

</div>