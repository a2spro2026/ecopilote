<section class="relative overflow-hidden bg-blue-950">
    <div class="absolute inset-0 opacity-20"
         style="background-image:radial-gradient(circle at 20% 30%, #34d399 0, transparent 40%), radial-gradient(circle at 80% 0%, #3b82f6 0, transparent 35%);"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <p class="text-sm font-medium text-emerald-300">ECOPILOTE</p>
        <h1 class="mt-2 text-3xl font-extrabold text-white sm:text-4xl" style="font-family:'Poppins',sans-serif;">{{ $title }}</h1>
        @isset($subtitle)
            <p class="mt-3 max-w-2xl text-blue-100">{{ $subtitle }}</p>
        @endisset
    </div>
</section>
