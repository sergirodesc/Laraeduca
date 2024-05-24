<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 dark:bg-gray-900">
        <div class="flex flex-col max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-row mb-4">
                {{--@if(Auth::user()->hasRole('admin'))--}}
                <div class="bg-gray-800 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg w-5/6 mr-4 h-60">
                    <p class="flex mt-2 ml-2 text-gray-200">
                        <span class="material-symbols-outlined mr-2">notifications_active</span>
                        Alertas
                    </p>
                    <div class="flex justify-center mt-2">
                        <p class="flex justify-start bg-green-300 dark:bg-green-600 rounded-full w-5/6 text-gray-900 dark:text-gray-200">
                            <span class="material-symbols-outlined ml-2 mr-2">info</span>
                            Welcome, {{ Auth::user()->name }}
                        </p>
                    </div>
                </div>
                {{--@endif--}}
                <div class="flex flex-row w-full h-screen  ">
                    <div class="bg-gray-800 dark:bg-gray-800  overflow-hidden shadow-xl sm:rounded-lg w-5/6 mr-4 h-60">
                        <p class="flex mt-2 ml-2 text-gray-200">
                            <span class="material-symbols-outlined mr-2">school</span>
                            Cursos
                        </p>
                        <div class="flex flex-col justify-center items-center mt-5">
                            <p class="flex justify-start bg-green-300 dark:bg-green-600 rounded-full w-5/6 mb-4 text-gray-900 dark:text-gray-200">
                                <span class="ml-4">M9 - Web Interface Design</span>
                            </p>
                            <p class="flex justify-start bg-blue-300 dark:bg-blue-600 rounded-full w-5/6 text-gray-900 dark:text-gray-200">
                                <span class="ml-4">M6 - Web in Client Environment</span>
                            </p>

                        </div>
                        <div class="flex flex-col justify-center items-center mt-4">
                            <p class="flex justify-start bg-green-300 dark:bg-yellow-600 rounded-full w-5/6 mb-4 text-gray-900 dark:text-gray-200">
                                <span class="ml-4">M12 - Project</span>
                            </p>
                            <p class="flex justify-start bg-blue-300 dark:bg-red-600 rounded-full w-5/6 text-gray-900 dark:text-gray-200">
                                <span class="ml-4">M3 - Java Script</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="bg-gray-800 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg w-4/6 h-60">
                    <p class="flex mt-2 ml-2 mb-2 text-gray-200"><span class="material-symbols-outlined mr-2">grade</span> Latest
                        Grades
                    </p>
                    <div class="flex flex-row justify-evenly">
                        <div
                            class="flex flex-col justify-center items-center bg-gray-700 dark:bg-gray-700 w-1/4 h-40 rounded-lg shadow-xl">
                            <p class="text-gray-200">M9 - Tailwind CSS</p>
                            <span
                                class="flex justify-center items-center ml-2 w-[100px] h-[100px] bg-yellow-200 dark:bg-yellow-600 border border-yellow-300 dark:border-yellow-500 rounded-full relative text-gray-900 dark:text-gray-200">
                                60%
                            </span>
                        </div>
                        <div
                            class="flex flex-col justify-center items-center bg-gray-700 dark:bg-gray-700 w-1/4 h-40 rounded-lg shadow-xl">
                            <p class="text-gray-200">M6 - Canvas Game</p>
                            <span
                                class="flex justify-center items-center ml-2 w-[100px] h-[100px] bg-green-300 dark:bg-green-600 border border-green-400 dark:border-green-500 rounded-full relative text-gray-900 dark:text-gray-200">
                                100%
                            </span>
                        </div>
                        <div
                            class="flex flex-col justify-center items-center bg-gray-700 dark:bg-gray-700 w-1/4 h-40 rounded-lg shadow-xl">
                            <p class="text-gray-200">M8 - Docker</p>
                            <span
                                class="flex justify-center items-center ml-2 w-[100px] h-[100px] bg-red-300 dark:bg-red-600 border border-red-400 dark:border-red-500 rounded-full relative text-gray-900 dark:text-gray-200">
                                30%
                            </span>
                        </div>
                    </div>
                </div> -->

        </div>
</x-app-layout>