<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @auth
                @if(auth()->user()->is_admin)
                    <div class="bg-red-600 text-white text-center font-bold p-3 rounded-lg shadow-md my-4">
                        Modo ADMINISTRADOR. Estas accediendo a contenido PRIVADO, en caso de estar viendo esto y no contar con las los permisos necesarios, se tomarán ACCIONES LEGALES.
                    </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-4 my-8">

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa Cube</h3>
                                        <p class="text-xs text-indigo-200 mt-1">i-dle</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Soyeon, Soojin, Minnie, Miyeon, Yuqi, Shuhua</p>
                                            <img
                                                src="{{ asset('imagenes/Grafica1.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-600 rounded-full"></span> (G)I-DLE debutó oficialmente el 2 de mayo de 2018 bajo Cube Entertainment. El grupo lanzó su primer mini álbum, titulado "I Am", junto con el sencillo principal "LATATA", el cual se convirtió rápidamente en un éxito.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa Starship</h3>
                                        <p class="text-xs text-indigo-200 mt-1">IVE</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Yujin, Gaeul, Rei, Liz, Wonyoung, Leseoo</p>
                                            <img
                                                src="{{ asset('imagenes/Grafica2.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> El grupo femenino surcoreano IVE (formado por Starship Entertainment) debutó oficialmente el 1 de diciembre de 2021
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa JYP</h3>
                                        <p class="text-xs text-indigo-200 mt-1">TWICE</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Jihyo, Momo, Sana, Dahyun, Tsuyu, Chaeyoung, Mina, Jeongyeon, Nayeon</p>
                                            <img
                                                src="{{ asset('imagenes/Grafica3.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> Twice debutó oficialmente el 20 de octubre de 2015 bajo JYP Entertainment, tras ser formadas en el programa de supervivencia SIXTEEN. Lo hicieron lanzando su miniálbum debut The Story Begins y su icónico sencillo principal, «Like OOH-AHH»
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-4 my-8">

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa JYP</h3>
                                        <p class="text-xs text-indigo-200 mt-1">NMIXX</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Haewon, Lily, Sullyoon, Bae, Jiwoo, Kyujin</p>
                                            <img
                                                src="{{ asset('imagenes/Grafica4.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-600 rounded-full"></span> El grupo de K-pop NMIXX debutó oficialmente el 22 de febrero de 2022 con su primer álbum sencillo titulado Ad Mare y su canción principal «O.O»
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa YG</h3>
                                        <p class="text-xs text-indigo-200 mt-1">BLACKPINK</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Lisa, Jenie, Jisoo, Rosé</p>
                                            <img
                                                src="{{ asset('imagenes/Grafica5.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> El grupo surcoreano BLACKPINK hizo su debut oficial el 8 de agosto de 2016 con el lanzamiento de su álbum sencillo Square One, el cual incluyó los exitosos temas "Boombayah" y "Whistle". Desde entonces, se han convertido en uno de los grupos más grandes de la industria global del K-pop
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa HYBE</h3>
                                        <p class="text-xs text-indigo-200 mt-1">Le Sserafim</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Sakura, Chaewon, Yunjin, Kazuha, Eunchae</p>
                                            <img
                                                src="{{ asset('imagenes/Grafica6.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span>El debut oficial de LE SSERAFIM en Corea fue el 2 de mayo de 2022 con el miniálbum FEARLESS, cuyo sencillo principal lleva el mismo nombre.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif
            @endauth


            @auth
                    @if(!auth()->user()->is_admin)
                        <div class="text-black-700 p-4 rounded-lg shadow-sm mb-6 font-medium" role="alert">
                            ¡BIENVENIDO!
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-4 my-8">

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa Cube</h3>
                                        <p class="text-xs text-indigo-200 mt-1">i-dle</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Soyeon, Soojin, Minnie, Miyeon, Yuqi, Shuhua</p>
                                            <img
                                                src="{{ asset('imagenes/(G)I-dle_logo.svg.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-600 rounded-full"></span> (G)I-DLE debutó oficialmente el 2 de mayo de 2018 bajo Cube Entertainment. El grupo lanzó su primer mini álbum, titulado "I Am", junto con el sencillo principal "LATATA", el cual se convirtió rápidamente en un éxito.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa Starship</h3>
                                        <p class="text-xs text-indigo-200 mt-1">IVE</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Yujin, Gaeul, Rei, Liz, Wonyoung, Leseoo</p>
                                            <img
                                                src="{{ asset('imagenes/ive_logo.jpg') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> El grupo femenino surcoreano IVE (formado por Starship Entertainment) debutó oficialmente el 1 de diciembre de 2021
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa JYP</h3>
                                        <p class="text-xs text-indigo-200 mt-1">TWICE</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Jihyo, Momo, Sana, Dahyun, Tsuyu, Chaeyoung, Mina, Jeongyeon, Nayeon</p>
                                            <img
                                                src="{{ asset('imagenes/TWICE-Logo.png') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> Twice debutó oficialmente el 20 de octubre de 2015 bajo JYP Entertainment, tras ser formadas en el programa de supervivencia SIXTEEN. Lo hicieron lanzando su miniálbum debut The Story Begins y su icónico sencillo principal, «Like OOH-AHH»
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-4 my-8">

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa JYP</h3>
                                        <p class="text-xs text-indigo-200 mt-1">NMIXX</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Haewon, Lily, Sullyoon, Bae, Jiwoo, Kyujin</p>
                                            <img
                                                src="{{ asset('imagenes/nmixx_logo.jpg') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-600 rounded-full"></span> El grupo de K-pop NMIXX debutó oficialmente el 22 de febrero de 2022 con su primer álbum sencillo titulado Ad Mare y su canción principal «O.O»
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa YG</h3>
                                        <p class="text-xs text-indigo-200 mt-1">BLACKPINK</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Lisa, Jenie, Jisoo, Rosé</p>
                                            <img
                                                src="{{ asset('imagenes/blackpink_logo.jpg') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> El grupo surcoreano BLACKPINK hizo su debut oficial el 8 de agosto de 2016 con el lanzamiento de su álbum sencillo Square One, el cual incluyó los exitosos temas "Boombayah" y "Whistle". Desde entonces, se han convertido en uno de los grupos más grandes de la industria global del K-pop
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                                <div>
                                    <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                        <h3 class="text-lg font-bold tracking-wide uppercase">Empresa HYBE</h3>
                                        <p class="text-xs text-indigo-200 mt-1">Le Sserafim</p>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div >
                                            <p class="font-semibold text-gray-800 dark:text-black">Sakura, Chaewon, Yunjin, Kazuha, Eunchae</p>
                                            <img
                                                src="{{ asset('imagenes/lesserafim_logo.jpg') }}" 
                                                alt="Descripción de mi imagen" 
                                                class="w-full h-50 object-cover"
                                            />
                                        </div>
                                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                            <li class="flex items-center gap-2">
                                                <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span>El debut oficial de LE SSERAFIM en Corea fue el 2 de mayo de 2022 con el miniálbum FEARLESS, cuyo sencillo principal lleva el mismo nombre.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
            @endauth


            @guest
                <div class="text-black-700 p-4 rounded-lg shadow-sm mb-6 font-medium" role="alert">
                    Iniciaste como invitado. Para ver más funciones inicia sesión o regístrate.
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-4 my-8">

                    <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                <h3 class="text-lg font-bold tracking-wide uppercase">Empresa Cube</h3>
                                <p class="text-xs text-indigo-200 mt-1">i-dle</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div >
                                    <p class="font-semibold text-gray-800 dark:text-black">Soyeon, Soojin, Minnie, Miyeon, Yuqi, Shuhua</p>
                                    <img
                                        src="{{ asset('imagenes/(G)I-dle_logo.svg.png') }}" 
                                        alt="Descripción de mi imagen" 
                                        class="w-full h-50 object-cover"
                                    />
                                </div>
                                <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.9 h-1.5 bg-indigo-600 rounded-full"></span> (G)I-DLE debutó oficialmente el 2 de mayo de 2018 bajo Cube Entertainment. El grupo lanzó su primer mini álbum, titulado "I Am", junto con el sencillo principal "LATATA", el cual se convirtió rápidamente en un éxito.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                     <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                <h3 class="text-lg font-bold tracking-wide uppercase">Empresa Starship</h3>
                                <p class="text-xs text-indigo-200 mt-1">IVE</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div >
                                    <p class="font-semibold text-gray-800 dark:text-black">Yujin, Gaeul, Rei, Liz, Wonyoung, Leseoo</p>
                                    <img
                                        src="{{ asset('imagenes/ive_logo.jpg') }}" 
                                        alt="Descripción de mi imagen" 
                                        class="w-full h-50 object-cover"
                                    />
                                </div>
                                <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> El grupo femenino surcoreano IVE (formado por Starship Entertainment) debutó oficialmente el 1 de diciembre de 2021
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                <h3 class="text-lg font-bold tracking-wide uppercase">Empresa JYP</h3>
                                <p class="text-xs text-indigo-200 mt-1">TWICE</p>
                            </div>
                            <div class="p-6 space-y-4">
                                <div >
                                    <p class="font-semibold text-gray-800 dark:text-black">Jihyo, Momo, Sana, Dahyun, Tsuyu, Chaeyoung, Mina, Jeongyeon, Nayeon</p>
                                    <img
                                        src="{{ asset('imagenes/TWICE-Logo.png') }}" 
                                        alt="Descripción de mi imagen" 
                                        class="w-full h-50 object-cover"
                                    />
                                </div>
                                <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.9 h-1.5 bg-indigo-500 rounded-full"></span> Twice debutó oficialmente el 20 de octubre de 2015 bajo JYP Entertainment, tras ser formadas en el programa de supervivencia SIXTEEN. Lo hicieron lanzando su miniálbum debut The Story Begins y su icónico sencillo principal, «Like OOH-AHH»
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-white-800 rounded-xl shadow-lg border border-gray-20 dark:border-gray-00 overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="bg-indigo-600 dark:bg-indigo-800 text-white p-6 text-center">
                                                        <a href="{{ route('login') }}" class="font-semibold text-gray-100 hover:text-gray-950 dark:text-gray-100 dark:hover:text-white transition duration-150 ease-in-out">
                                                            Ver más....
                                                        </a>
                                                        
                            </div>
                        </div>
                </div>
            @endguest

            
        </h2>
    </x-slot>


@if(!auth()->check())
    <script>
        if (window.name !== 'pestaña_invitado_activa') {
            
            if (sessionStorage.getItem('flujo_invitado_valido') === 'true') {
                window.name = 'pestaña_invitado_activa';
                sessionStorage.removeItem('flujo_invitado_valido');
            } else {
                window.location.href = "{{ route('invitado.checkpoint') }}";
            }
        }
    </script>
@endif
    
</x-app-layout>