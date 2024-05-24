<x-app-layout>

    <div class="flex flex-row">

        <div
            class="bg-white overflow-hidden shadow-xl sm:rounded-lg w-2/6 mr-4 h-60 hover:scale-105 transition duration-200 ease-it-out">
            <a href="{{ route('music-task') }}" :active="request() -> routeIs('music-task')">
                <p class="flex mt-2 ml-2"><span class="material-symbols-outlined mr-2">music_note</span>
                    Piano Tiles
                </p>
                <div class="flex justify-center mt-2">
                    <img src="{{ asset('assets/music-img.png') }}" alt="music task">
                </div>
            </a>
        </div>

        <div
            class="bg-white overflow-hidden shadow-xl sm:rounded-lg w-2/6 mr-4 h-60 hover:scale-105 transition duration-200 ease-it-out">
            <a href="{{ route('simon-task') }}" :active="request() -> routeIs('simon-task')">
                <p class="flex mt-2 ml-2"><span class="material-symbols-outlined mr-2">calculate</span>
                    Simon Says
                </p>
                <div class="flex justify-center mt-2">
                    <img src="{{ asset('assets/simon-task.png') }}" alt="simon task">
                </div>
            </a>
        </div>

        <div
            class="bg-white overflow-hidden shadow-xl sm:rounded-lg w-2/6 mr-4 h-60 hover:scale-105 transition duration-200 ease-it-out">
            <a href="{{ route('hangman-task') }}" :active="request() -> routeIs('hangman-task')">
                <p class="flex mt-2 ml-2"><span class="material-symbols-outlined mr-2">function</span>
                    Drag And Drop
                </p>
                <div class="flex justify-center mt-2">
                    <img src="{{ asset('assets/hangman-task.png') }}" alt="simon task">
                </div>
            </a>
        </div>
    </div>

</x-app-layout>
