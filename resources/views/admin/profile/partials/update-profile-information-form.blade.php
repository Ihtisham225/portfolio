<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Avatar Upload with Modern Preview --}}
        <x-input-label for="avatar" :value="__('Avatar')" />
        <div 
            x-data="{ 
                previewUrl: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}', 
                hover: false 
            }"
            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 flex flex-col items-center justify-center text-center transition hover:border-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-800"
        >
            <x-input-label for="avatar" :value="__('User Avatar')" class="sr-only" />

            {{-- Preview area --}}
            <template x-if="previewUrl">
                <div class="relative">
                    <img :src="previewUrl" :alt="'Avatar of {{ $user->name }}'" class="w-28 h-28 rounded-xl object-cover shadow-md border border-gray-200 dark:border-gray-700 transition duration-200" />
                    <button 
                        type="button" 
                        @click="previewUrl = ''" 
                        class="absolute -top-2 -right-2 bg-gray-800/80 text-white rounded-full p-1 hover:bg-red-500 transition"
                        title="Remove image"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>

            {{-- Upload button --}}
            <div x-show="!previewUrl" class="flex flex-col items-center justify-center gap-2">
                <div class="flex items-center justify-center w-16 h-16 bg-indigo-50 dark:bg-gray-700 rounded-full">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-semibold text-indigo-600 dark:text-indigo-400 cursor-pointer hover:underline"
                        @click="$refs.avatarInput.click()">Click to upload</span> or drag and drop
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-500">PNG, JPG up to 2MB</p>
            </div>

            {{-- Hidden input --}}
            <input 
                type="file" 
                id="avatar" 
                name="avatar" 
                class="hidden" 
                accept="image/*" 
                x-ref="avatarInput"
                @change="
                    const file = $event.target.files[0];
                    if (file) previewUrl = URL.createObjectURL(file);
                "
            />

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
