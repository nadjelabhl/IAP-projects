<x-app-layout :header="'Mon Profil'">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl shadow-soft p-8">
            <h2 class="text-2xl font-black text-iap-blue mb-6">Mon Profil</h2>

            @if(session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Rôle</label>
                    <input type="text" value="{{ $user->role_label }}" disabled
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-500">
                </div>

                <hr class="my-6">

                <h3 class="text-lg font-bold text-slate-700 mb-4">Changer le mot de passe</h3>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nouveau mot de passe</label>
                    <input type="password" name="password"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="w-full bg-iap-blue text-white font-bold py-3 rounded-xl hover:bg-blue-900 transition">
                    Mettre à jour
                </button>
            </form>
        </div>
    </div>
</x-app-layout>