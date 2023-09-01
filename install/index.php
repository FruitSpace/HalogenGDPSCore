<html>
<head>
    <title>Install HalogenCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css" />
    <script src="app.js"></script>
</head>
<body class="flex min-h-screen flex-col items-center bg-gray-950 px-10 py-8 text-gray-50">
    <div class="flex space-x-2.5 rounded-[1.375rem] bg-gray-900 p-2.5 text-sm font-medium">
        <div class="rounded-xl px-5 py-2.5 transition-all" id="step-0">Info</div>
        <div class="rounded-xl px-5 py-2.5 transition-all" id="step-1">DB</div>
        <div class="rounded-xl px-5 py-2.5 transition-all" id="step-2">API</div>
        <div class="rounded-xl px-5 py-2.5 transition-all" id="step-3">Chests</div>
        <div class="rounded-xl px-5 py-2.5 transition-all" id="step-4">Other</div>
    </div>

    <!-- General Information -->

    <div id="content-0" class="hidden transition-all mt-12 w-full max-w-md flex-col space-y-6 rounded-[1.375rem] bg-gray-900 p-6">
        <p class="text-lg text-center">Установка HalogenCore</p>
        <p>Привет</p>
        <p>1. (Обязательно) Данные от базы данных MySQL. Создайте пользователя и базу данных для GDPS или узнайте их у вашего хостинга</p>
        <p>2. Если вы хотите использовать кастомную музыку, получите API ключ FruitSpace</p>

        <div>
            <span>Необходимые расширения:</span>
            <ul>
                <?php
                $checksvg='<svg class="h-[1rem]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path fill="currentcolor" d="M 26.980469 5.9902344 A 1.0001 1.0001 0 0 0 26.292969 6.2929688 L 11 21.585938 L 4.7070312 15.292969 A 1.0001 1.0001 0 1 0 3.2929688 16.707031 L 10.292969 23.707031 A 1.0001 1.0001 0 0 0 11.707031 23.707031 L 27.707031 7.7070312 A 1.0001 1.0001 0 0 0 26.980469 5.9902344 z"/></svg>';
                $crosssvg='<svg class="h-[1rem]" viewBox="-2 -4 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="currentcolor" d="M0 14.545L1.455 16 8 9.455 14.545 16 16 14.545 9.455 8 16 1.455 14.545 0 8 6.545 1.455 0 0 1.455 6.545 8z"/></svg>';
                $modules = get_loaded_extensions();
                $needed = ["mysqli","mbstring"]; //@m41denx: вообще без понятия про зависимости
                $block = false;
                foreach ($needed as $mod) {
                    $ok = in_array($mod, $modules);
                    if(!$ok) $block=true;
                    echo '<li class="flex items-center gap-1 text-'.($ok?"green":"red").'-500">'.($ok?$checksvg:$crosssvg).$mod.'</li>';
                }
                ?>
            </ul>
        </div>


        <div class="flex items-center justify-end space-x-2.5">
            <button onclick="pageNext()" class="<?=($block?"!bg-gray-500":"")?> rounded-xl flex items-center bg-blue-600 hover:bg-blue-700 transition-all px-5 py-2.5 text-sm font-medium" <?=($block?"disabled":"")?>>
                Next
                <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 12l14 0"></path>
                    <path d="M13 18l6 -6"></path>
                    <path d="M13 6l6 6"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- DB Setup -->

    <div id="content-1" class="hidden mt-12 flex w-full max-w-md flex-col space-y-6 rounded-[1.375rem] bg-gray-900 p-6">
        <div class="flex flex-col">
            <label for="db-host" class="text-sm font-medium">DB Host</label>
            <span class="flex gap-2">
                <input id="db-host" class="flex-1 border-b border-gray-600 bg-transparent py-2 focus:border-blue-600 focus:outline-none" placeholder="127.0.0.1" />
                <input id="db-port" class="border-b text-center border-gray-600 w-[4rem] bg-transparent py-2 focus:border-blue-600 focus:outline-none" placeholder="3306" />
            </span>
        </div>

        <div class="flex flex-col">
            <label for="db-user" class="text-sm font-medium">DB Username</label>
            <input id="db-user" class="border-b border-gray-600 bg-transparent py-2 focus:border-blue-600 focus:outline-none" placeholder="mysql_user" />
        </div>

        <div class="flex flex-col">
            <label for="db-pass" class="text-sm font-medium">DB Password</label>
            <input id="db-pass" class="border-b border-gray-600 bg-transparent py-2 focus:border-blue-600 focus:outline-none" placeholder="********" type="password" />
        </div>

        <div class="flex flex-col">
            <label for="db-name" class="text-sm font-medium">DB Name</label>
            <input id="db-name" class="border-b border-gray-600 bg-transparent py-2 focus:border-blue-600 focus:outline-none" placeholder="gdps_db" />
        </div>

        <div id="error-1" class="!hidden rounded-xl bg-red-500 px-5 py-2 flex gap-2 items-center"></div>
        <div class="flex items-center justify-end space-x-2.5">
            <div class="loader mr-auto"></div>
            <button onclick="pagePrev()" class="rounded-xl px-5 py-2.5 hover:bg-gray-800 transition-all text-sm font-medium">Back</button>
            <button onclick="pageNext()" class="rounded-xl flex items-center bg-blue-600 hover:bg-blue-700 transition-all px-5 py-2.5 text-sm font-medium">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 12l14 0"></path>
                    <path d="M13 18l6 -6"></path>
                    <path d="M13 6l6 6"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- API -->
    <div id="content-2" class="hidden transition-all mt-12 w-full max-w-md flex-col space-y-6 rounded-[1.375rem] bg-gray-900 p-6">
        <p>Тут ничего нет</p>


        <div class="flex items-center justify-end space-x-2.5">
            <button onclick="pagePrev()" class="rounded-xl px-5 py-2.5 hover:bg-gray-800 transition-all text-sm font-medium">Back</button>
            <button onclick="pageNext()" class="rounded-xl flex items-center bg-blue-600 hover:bg-blue-700 transition-all px-5 py-2.5 text-sm font-medium">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 12l14 0"></path>
                    <path d="M13 18l6 -6"></path>
                    <path d="M13 6l6 6"></path>
                </svg>
            </button>
        </div>
    </div>

    <div id="content-3" class="hidden transition-all mt-12 w-full max-w-md flex-col space-y-6 rounded-[1.375rem] bg-gray-900 p-6">
        <p>Тут ничего нет</p>


        <div class="flex items-center justify-end space-x-2.5">
            <button onclick="pagePrev()" class="rounded-xl px-5 py-2.5 hover:bg-gray-800 transition-all text-sm font-medium">Back</button>
            <button onclick="pageNext()" class="rounded-xl flex items-center bg-blue-600 hover:bg-blue-700 transition-all px-5 py-2.5 text-sm font-medium">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 12l14 0"></path>
                    <path d="M13 18l6 -6"></path>
                    <path d="M13 6l6 6"></path>
                </svg>
            </button>
        </div>
    </div>

    <div id="content-4" class="hidden transition-all mt-12 w-full max-w-md flex-col space-y-6 rounded-[1.375rem] bg-gray-900 p-6">
        <p>Тут ничего нет</p>


        <div class="flex items-center justify-end space-x-2.5">
            <button onclick="pagePrev()" class="rounded-xl px-5 py-2.5 hover:bg-gray-800 transition-all text-sm font-medium">Back</button>
            <button onclick="pageNext()" class="rounded-xl flex items-center bg-blue-600 hover:bg-blue-700 transition-all px-5 py-2.5 text-sm font-medium">
                Done
            </button>
        </div>
    </div>

</body>
</html>