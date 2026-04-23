// assets/js/scanner.js
// Assure-toi d'inclure <script src="https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js"></script> dans la page
const codeReader = new ZXing.BrowserMultiFormatReader();
const videoElem = document.getElementById('video');
const detected = document.getElementById('detected');

async function startScan() {
  try {
    const devices = await ZXing.BrowserCodeReader.listVideoInputDevices();
    const deviceId = devices.length ? devices[0].deviceId : undefined;
    codeReader.decodeFromVideoDevice(deviceId, videoElem, (result, err) => {
      if (result) {
        const code = result.getText();
        detected.textContent = code;
        addBarcodeToCart(code, 1);
      }
    });
  } catch (e) {
    alert('Erreur caméra: ' + e);
  }
}

function stopScan() { codeReader.reset(); }

function addBarcodeToCart(barcode, qty) {
  const fd = new FormData();
  fd.append('action','add_to_cart');
  fd.append('barcode', barcode);
  fd.append('qty', qty);
  fetch('/facturation/modules/produits/enregistrer.php', {method:'POST', body:fd})
    .then(r=>r.json()).then(res=>{
      if (!res.ok) alert(res.error || 'Erreur');
      else location.reload();
    });
}
