@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
        <div class="mx-auto max-w-7xl px-4">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
                🌍 Mapa mundi — selecciona un país
            </h1>

            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                <div id="svg-wrapper" class="w-full overflow-hidden relative">
                    @php
                        $svgPath = public_path('simplemaps/world.svg');
                        echo file_exists($svgPath)
                          ? file_get_contents($svgPath)
                          : '<p class="text-red-600 text-sm">No se encontró /public/simplemaps/world.svg</p>';
                    @endphp
                </div>

                {{-- Tooltip flotante --}}
                <div id="country-tooltip"
                     class="pointer-events-none hidden fixed z-50 rounded-lg border border-gray-200 bg-white px-3 py-1.5 shadow
                  text-xs font-medium text-gray-900
                  dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <span id="country-tooltip-name"></span>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal popup --}}
    <div id="country-modal" tabindex="-1" aria-hidden="true"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Información del país</h3>
                <button id="modal-close" class="text-2xl leading-none text-gray-500 hover:text-gray-800 dark:hover:text-gray-200">&times;</button>
            </div>
            <div class="space-y-3 text-center">
                <span id="modal-flag" class="fi fi-xx w-10 h-7 mx-auto hidden"></span>
                <p id="modal-name" class="text-xl font-medium text-gray-900 dark:text-gray-100">—</p>
                <p id="modal-id" class="text-sm text-gray-600 dark:text-gray-300">—</p>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const svg       = document.querySelector('#svg-wrapper svg');
            const tooltip   = document.getElementById('country-tooltip');
            const tipName   = document.getElementById('country-tooltip-name');

            // Si tienes modal/panel, define aquí tus refs:
            const modal     = document.getElementById('country-modal');      // opcional
            const modalName = document.getElementById('modal-name');         // opcional
            const modalId   = document.getElementById('modal-id');           // opcional
            const modalFlag = document.getElementById('modal-flag');         // opcional
            const modalClose= document.getElementById('modal-close');        // opcional

            if (!svg) return;

            // === Usa tu NAME_MAP tal cual lo pegaste arriba ===
            const NAME_MAP = {
                AF:"Afghanistan", AX:"Åland Islands", AL:"Albania", DZ:"Algeria", AS:"American Samoa", AD:"Andorra", AO:"Angola", AI:"Anguilla", AQ:"Antarctica", AG:"Antigua and Barbuda", AR:"Argentina", AM:"Armenia", AW:"Aruba", AU:"Australia", AT:"Austria", AZ:"Azerbaijan",
                BS:"Bahamas", BH:"Bahrain", BD:"Bangladesh", BB:"Barbados", BY:"Belarus", BE:"Belgium", BZ:"Belize", BJ:"Benin", BM:"Bermuda", BT:"Bhutan", BO:"Bolivia", BQ:"Bonaire, Sint Eustatius and Saba", BA:"Bosnia and Herzegovina", BW:"Botswana", BR:"Brazil", IO:"British Indian Ocean Territory", VG:"British Virgin Islands", BN:"Brunei Darussalam", BG:"Bulgaria", BF:"Burkina Faso", BI:"Burundi",
                KH:"Cambodia", CM:"Cameroon", CA:"Canada", CV:"Cabo Verde", KY:"Cayman Islands", CF:"Central African Republic", TD:"Chad", CL:"Chile", CN:"China", CX:"Christmas Island", CC:"Cocos (Keeling) Islands", CO:"Colombia", KM:"Comoros", CG:"Congo", CD:"Congo (Democratic Republic of the)", CK:"Cook Islands", CR:"Costa Rica", CI:"Côte d'Ivoire", HR:"Croatia", CU:"Cuba", CW:"Curaçao", CY:"Cyprus", CZ:"Czechia",
                DK:"Denmark", DJ:"Djibouti", DM:"Dominica", DO:"Dominican Republic",
                EC:"Ecuador", EG:"Egypt", SV:"El Salvador", GQ:"Equatorial Guinea", ER:"Eritrea", EE:"Estonia", SZ:"Eswatini", ET:"Ethiopia",
                FK:"Falkland Islands", FO:"Faroe Islands", FJ:"Fiji", FI:"Finland", FR:"France", GF:"French Guiana", PF:"French Polynesia", TF:"French Southern Territories",
                GA:"Gabon", GM:"Gambia", GE:"Georgia", DE:"Germany", GH:"Ghana", GI:"Gibraltar", GR:"Greece", GL:"Greenland", GD:"Grenada", GP:"Guadeloupe", GU:"Guam", GT:"Guatemala", GG:"Guernsey", GN:"Guinea", GW:"Guinea-Bissau", GY:"Guyana",
                HT:"Haiti", HM:"Heard Island and McDonald Islands", VA:"Holy See", HN:"Honduras", HK:"Hong Kong", HU:"Hungary",
                IS:"Iceland", IN:"India", ID:"Indonesia", IR:"Iran", IQ:"Iraq", IE:"Ireland", IM:"Isle of Man", IL:"Israel", IT:"Italy",
                JM:"Jamaica", JP:"Japan", JE:"Jersey", JO:"Jordan",
                KZ:"Kazakhstan", KE:"Kenya", KI:"Kiribati", KP:"North Korea", KR:"South Korea", KW:"Kuwait", KG:"Kyrgyzstan",
                LA:"Laos", LV:"Latvia", LB:"Lebanon", LS:"Lesotho", LR:"Liberia", LY:"Libya", LI:"Liechtenstein", LT:"Lithuania", LU:"Luxembourg",
                MO:"Macao", MG:"Madagascar", MW:"Malawi", MY:"Malaysia", MV:"Maldives", ML:"Mali", MT:"Malta", MH:"Marshall Islands", MQ:"Martinique", MR:"Mauritania", MU:"Mauritius", YT:"Mayotte", MX:"Mexico", FM:"Micronesia", MD:"Moldova", MC:"Monaco", MN:"Mongolia", ME:"Montenegro", MS:"Montserrat", MA:"Morocco", MZ:"Mozambique", MM:"Myanmar",
                NA:"Namibia", NR:"Nauru", NP:"Nepal", NL:"Netherlands", NC:"New Caledonia", NZ:"New Zealand", NI:"Nicaragua", NE:"Niger", NG:"Nigeria", NU:"Niue", NF:"Norfolk Island", MK:"North Macedonia", MP:"Northern Mariana Islands", NO:"Norway",
                OM:"Oman",
                PK:"Pakistan", PW:"Palau", PS:"Palestine", PA:"Panama", PG:"Papua New Guinea", PY:"Paraguay", PE:"Peru", PH:"Philippines", PN:"Pitcairn", PL:"Poland", PT:"Portugal", PR:"Puerto Rico",
                QA:"Qatar",
                RE:"Réunion", RO:"Romania", RU:"Russia", RW:"Rwanda",
                BL:"Saint Barthélemy", SH:"Saint Helena", KN:"Saint Kitts and Nevis", LC:"Saint Lucia", MF:"Saint Martin", PM:"Saint Pierre and Miquelon", VC:"Saint Vincent and the Grenadines", WS:"Samoa", SM:"San Marino", ST:"São Tomé and Príncipe", SA:"Saudi Arabia", SN:"Senegal", RS:"Serbia", SC:"Seychelles", SL:"Sierra Leone", SG:"Singapore", SX:"Sint Maarten", SK:"Slovakia", SI:"Slovenia", SB:"Solomon Islands", SO:"Somalia", ZA:"South Africa", GS:"South Georgia", SS:"South Sudan", ES:"Spain", LK:"Sri Lanka", SD:"Sudan", SR:"Suriname", SJ:"Svalbard and Jan Mayen", SE:"Sweden", CH:"Switzerland", SY:"Syria",
                TW:"Taiwan", TJ:"Tajikistan", TZ:"Tanzania", TH:"Thailand", TL:"Timor-Leste", TG:"Togo", TK:"Tokelau", TO:"Tonga", TT:"Trinidad and Tobago", TN:"Tunisia", TR:"Turkey", TM:"Turkmenistan", TC:"Turks and Caicos Islands", TV:"Tuvalu",
                UG:"Uganda", UA:"Ukraine", AE:"United Arab Emirates", GB:"United Kingdom", US:"United States", UY:"Uruguay", UZ:"Uzbekistan",
                VU:"Vanuatu", VE:"Venezuela", VN:"Vietnam",
                WF:"Wallis and Futuna", EH:"Western Sahara",
                YE:"Yemen",
                ZM:"Zambia", ZW:"Zimbabwe",
                XK:"Kosovo"
            };

            // === 1) Responsivo + permitir eventos ===
            svg.removeAttribute('width'); svg.removeAttribute('height');
            svg.setAttribute('preserveAspectRatio','xMidYMid meet');
            svg.classList.add('w-full','h-auto');

            // Quita bloqueos de eventos si existen en el SVG
            svg.querySelectorAll('style').forEach(st => {
                st.textContent = (st.textContent || '').replace(/pointer-events\s*:\s*none\s*;?/gi, '');
            });
            svg.querySelectorAll('[pointer-events="none"]').forEach(n => n.removeAttribute('pointer-events'));
            svg.querySelectorAll('path, polygon, polyline').forEach(n => { n.style.pointerEvents = 'auto'; });

            // === 2) Normalización de IDs (soporta US-1, usa, USA, CHN, AU-main, etc.) ===
            const ISO3_TO_ISO2 = { USA:'US', RUS:'RU', CHN:'CN', AUS:'AU', CAN:'CA', BRA:'BR', MEX:'MX', ARG:'AR', GBR:'GB', DEU:'DE', FRA:'FR', ITA:'IT', ESP:'ES', JPN:'JP', KOR:'KR', PRK:'KP', ARE:'AE', COD:'CD', COG:'CG' };
            const SPECIAL      = { UK:'GB', EL:'GR' }; // alias comunes (UK→GB, EL→GR)

            function normalizeId(raw) {
                if (!raw) return null;
                let id = String(raw).trim();

                // Si empieza con 2 letras: US-1, cn-main
                const m2 = id.match(/^([A-Za-z]{2})\b/);
                if (m2) {
                    id = m2[1].toUpperCase();
                    if (SPECIAL[id]) id = SPECIAL[id];
                    if (NAME_MAP[id]) return id;
                }

                // Si empieza con 3 letras ISO3: USA, RUS, CHN
                const m3 = id.match(/^([A-Za-z]{3})\b/);
                if (m3) {
                    const iso3 = m3[1].toUpperCase();
                    if (ISO3_TO_ISO2[iso3]) return ISO3_TO_ISO2[iso3];
                }

                // Último intento: todo mayúsculas
                id = id.toUpperCase();
                if (SPECIAL[id]) id = SPECIAL[id];
                if (NAME_MAP[id]) return id;

                return null;
            }

            // Sube por los padres hasta hallar un id de país válido
            function closestCountryEl(start) {
                let el = start;
                while (el && el !== svg) {
                    const norm = normalizeId(el.id || '');
                    if (norm) return { el, iso2: norm };
                    el = el.parentElement;
                }
                return null;
            }

            // Si un overlay tapa el país, mira "debajo" del cursor
            function probeUnder(x, y, ignoreEl) {
                const prev = ignoreEl.style.pointerEvents;
                ignoreEl.style.pointerEvents = 'none';
                const under = document.elementFromPoint(x, y);
                ignoreEl.style.pointerEvents = prev || '';
                return under;
            }

            // === 3) Tooltip ===
            const OFFSET = 12;
            function showTooltip(text, x, y){
                tipName.textContent = text || '—';
                tooltip.classList.remove('hidden');
                const rect = tooltip.getBoundingClientRect();
                let left = x + OFFSET, top = y + OFFSET;
                if (left + rect.width > innerWidth - 6) left = x - rect.width - OFFSET;
                if (top + rect.height > innerHeight - 6) top = y - rect.height - OFFSET;
                tooltip.style.left = `${left}px`;
                tooltip.style.top  = `${top}px`;
            }
            function hideTooltip(){ tooltip.classList.add('hidden'); }

            // === 4) Modal (opcional) ===
            function openModal(iso2){
                if (!modal) return; // si no tienes modal, omite
                modalName.textContent = NAME_MAP[iso2] || iso2;
                modalId.textContent   = iso2;
                if (modalFlag) {
                    modalFlag.className = "fi fi-" + iso2.toLowerCase();
                    modalFlag.classList.remove("hidden");
                }
                modal.classList.remove("hidden");
            }
            if (modalClose) {
                modalClose.addEventListener('click', () => modal.classList.add('hidden'));
                document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.add('hidden'); });
            }

            // === 5) Delegación de eventos en el <svg> (soporta <g> con múltiples <path>) ===
            svg.addEventListener('mousemove', (e) => {
                let hit = closestCountryEl(e.target);
                if (!hit) {
                    // prueba debajo si hay una capa encima
                    const under = probeUnder(e.clientX, e.clientY, e.target);
                    if (under) hit = closestCountryEl(under);
                }
                if (!hit) { hideTooltip(); return; }
                showTooltip(NAME_MAP[hit.iso2] || hit.iso2, e.clientX, e.clientY);
                hit.el.style.cursor = 'pointer';
            });

            svg.addEventListener('mouseleave', hideTooltip);

            svg.addEventListener('click', (e) => {
                let hit = closestCountryEl(e.target);
                if (!hit) {
                    const under = probeUnder(e.clientX, e.clientY, e.target);
                    if (under) hit = closestCountryEl(under);
                }
                if (!hit) return;
                openModal(hit.iso2);
            });

            // Accesible con teclado: marca focuseables los que tengan id mapeable
            svg.querySelectorAll('[id]').forEach(el => {
                const iso2 = normalizeId(el.id);
                if (iso2) {
                    el.setAttribute('tabindex', '0');
                    el.style.outline = 'none';
                    el.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            openModal(iso2);
                        }
                    });
                }
            });
        })();
    </script>

@endsection
