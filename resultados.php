<?php
// resultados.php
// 1. Compresión de salida para que el HTML llegue antes al navegador
if (!ob_start("ob_gzhandler")) ob_start();

ini_set('display_errors', 1);
ini_set('memory_limit', '512M'); 
error_reporting(E_ALL);

$origen = $_GET['origen'] ?? 'MAD';
$destinoIATA = $_GET['destino'] ?? 'LPA';
$fecha_ida = $_GET['fecha'] ?? '';
$fecha_vuelta = $_GET['fecha_vuelta'] ?? null;

$api_key = "duffel_test_2MC0WXCcYUXxbgkSSZLC17YgNimlXcpd7XrxYK9JWhf"; 

$slices = [["origin" => $origen, "destination" => $destinoIATA, "departure_date" => $fecha_ida]];
if (!empty($fecha_vuelta)) {
    $slices[] = ["origin" => $destinoIATA, "destination" => $origen, "departure_date" => $fecha_vuelta];
}

// MEJORA 1: Filtros de búsqueda para que la API trabaje menos
$postData = ["data" => [
    "slices" => $slices,
    "passengers" => [["type" => "adult"]],
    "cabin_class" => "economy",
    "max_connections" => 1 // <--- Solo directos o 1 escala. Esto acelera mucho la respuesta.
]];

$ch = curl_init("https://api.duffel.com/air/offer_requests?return_offers=true"); // return_offers=true ayuda en algunas versiones
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
    "Duffel-Version: v2",
    "Content-Type: application/json",
    "Accept-Encoding: gzip"
]);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
// MEJORA 2: Tiempo de espera (Timeout) para no bloquear el servidor
curl_setopt($ch, CURLOPT_TIMEOUT, 10); 

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
unset($response); 

// MEJORA 3: Limitar a 40 ofertas. Suficiente para el usuario y mucho más ligero de procesar.
$ofertas_reales = array_slice($data['data']['offers'] ?? [], 0, 40); 

usort($ofertas_reales, function($a, $b) {
    return $a['total_amount'] <=> $b['total_amount'];
});

// Paginación
$v_pag = 10;
$total = count($ofertas_reales);
$paginas = ceil($total / $v_pag);
$p_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$indice = ($p_actual - 1) * $v_pag;
$ofertas_finales = array_slice($ofertas_reales, $indice, $v_pag);

function link_p($n, $params) {
    $params['p'] = $n;
    return "?" . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Canary Travel - Resultados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optimización de renderizado */
        .flight-card { content-visibility: auto; }
    </style>
</head>
<body class="bg-slate-100 p-6">
    <div id="loading-screen" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-100">
    <div class="relative">
        <div class="text-6-xl animate-bounce">✈️</div>
        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin absolute -top-4 -left-4"></div>
    </div>
    <h2 class="mt-8 text-xl font-black text-slate-700 uppercase tracking-widest">Buscando en Canary Travel</h2>
    <p class="text-slate-400 animate-pulse">Conectando con las aerolíneas...</p>
    
    <div class="mt-10 w-full max-w-md space-y-4 px-6">
        <div class="h-24 bg-white rounded-3xl animate-pulse"></div>
        <div class="h-24 bg-white rounded-3xl animate-pulse opacity-50"></div>
    </div>
</div>

<script>
// Cuando la ventana esté totalmente cargada, ocultamos el loader
window.addEventListener('load', function() {
    const loader = document.getElementById('loading-screen');
    loader.style.transition = 'opacity 0.5s ease';
    loader.style.opacity = '0';
    setTimeout(() => loader.remove(), 500);
});
</script>
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-slate-800 uppercase italic"><?php echo $origen; ?> ✈ <?php echo $destinoIATA; ?></h1>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                <?php echo $total; ?> ofertas encontradas
            </span>
        </div>

        <?php if ($http_code !== 201): ?>
            <div class="bg-red-50 border border-red-200 p-6 rounded-2xl text-red-700">
                <p class="font-bold">ERROR AL BUSCAR VUELOS</p>
                <p class="text-sm">La API tardó demasiado o los datos son incorrectos.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($ofertas_finales as $v): 
                    $ida = $v['slices'][0];
                    $vuelta = $v['slices'][1] ?? null;
                    $logo = $v['owner']['logo_symbol_url'];

                    $ida_dep = new DateTime($ida['segments'][0]['departing_at']);
                    $ida_arr = new DateTime(end($ida['segments'])['arriving_at']);
                    
                    if ($vuelta) {
                        $vta_dep = new DateTime($vuelta['segments'][0]['departing_at']);
                        $vta_arr = new DateTime(end($vuelta['segments'])['arriving_at']);
                    }
                ?>
                <div class="flight-card bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="p-8">
                        <div class="flex items-center gap-6 mb-6">
                            <div class="w-20 text-center">
                                <img src="<?php echo $logo; ?>" class="w-12 h-12 mx-auto object-contain" loading="lazy">
                                <span class="text-[10px] font-bold text-slate-400 block mt-1 uppercase"><?php echo $v['owner']['name']; ?></span>
                            </div>
                            <div class="flex-1 grid grid-cols-3 items-center text-center">
                                <div class="text-left">
                                    <p class="text-2xl font-black"><?php echo $ida_dep->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo $origen; ?></p>
                                </div>
                                <div class="px-4">
                                    <p class="text-[10px] text-slate-400 font-bold italic"><?php echo str_replace(['PT','H','M'],['','h ','m'],$ida['duration']); ?></p>
                                    <div class="h-[1px] bg-slate-200 w-full relative my-2"><div class="absolute -top-[3.5px] right-0 w-2 h-2 rounded-full bg-blue-500"></div></div>
                                    <p class="text-[10px] text-green-500 font-bold uppercase">
                                        <?php echo count($ida['segments']) > 1 ? (count($ida['segments'])-1).' escala' : 'Directo'; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black"><?php echo $ida_arr->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo $destinoIATA; ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if ($vuelta): ?>
                        <div class="flex items-center gap-6 pt-6 border-t border-dashed border-slate-100">
                            <div class="w-20 text-center">
                                <img src="<?php echo $logo; ?>" class="w-12 h-12 mx-auto object-contain grayscale opacity-40" loading="lazy">
                                <span class="text-[10px] font-bold text-slate-300 block mt-1 uppercase italic">Vuelta</span>
                            </div>
                            <div class="flex-1 grid grid-cols-3 items-center text-center">
                                <div class="text-left">
                                    <p class="text-2xl font-black text-slate-600"><?php echo $vta_dep->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo $destinoIATA; ?></p>
                                </div>
                                <div class="px-4">
                                    <p class="text-[10px] text-slate-400 font-bold italic"><?php echo str_replace(['PT','H','M'],['','h ','m'],$vuelta['duration']); ?></p>
                                    <div class="h-[1px] bg-slate-200 w-full relative my-2"><div class="absolute -top-[3.5px] left-0 w-2 h-2 rounded-full bg-orange-400"></div></div>
                                    <p class="text-[10px] text-green-500 font-bold uppercase">
                                        <?php echo count($vuelta['segments']) > 1 ? (count($vuelta['segments'])-1).' escala' : 'Directo'; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-slate-600"><?php echo $vta_arr->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo $origen; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-slate-50 px-10 py-6 flex justify-between items-center border-t border-slate-100">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase">Precio Final</p>
                            <p class="text-4xl font-black text-blue-600"><?php echo number_format($v['total_amount'], 2, ',', '.'); ?> <span class="text-sm font-normal text-slate-400"><?php echo $v['total_currency']; ?></span></p>
                        </div>
                        <button class="bg-blue-600 text-white px-12 py-4 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-colors">Seleccionar</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-center gap-3 mt-12 pb-10">
                <?php if($paginas > 1): ?>
                    <?php for($i=1; $i<=$paginas; $i++): ?>
                        <a href="<?php echo link_p($i, $_GET); ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl font-bold <?php echo $i == $p_actual ? 'bg-blue-600 text-white shadow-xl' : 'bg-white text-slate-400 border border-slate-200 hover:border-blue-500 transition-all'; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>