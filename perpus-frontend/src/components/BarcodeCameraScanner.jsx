import { useCallback, useEffect, useRef, useState } from 'react';
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

const SUPPORTED_BARCODE_FORMATS = [
  Html5QrcodeSupportedFormats.CODE_128,
  Html5QrcodeSupportedFormats.CODE_39,
  Html5QrcodeSupportedFormats.CODE_93,
  Html5QrcodeSupportedFormats.EAN_13,
  Html5QrcodeSupportedFormats.EAN_8,
  Html5QrcodeSupportedFormats.UPC_A,
  Html5QrcodeSupportedFormats.UPC_E,
  Html5QrcodeSupportedFormats.ITF,
  Html5QrcodeSupportedFormats.CODABAR,
  Html5QrcodeSupportedFormats.QR_CODE,
];

const getPreferredCameraId = (cameras) => {
  const backCamera = cameras.find((camera) => {
    const label = (camera.label || '').toLowerCase();
    return label.includes('back') || label.includes('rear') || label.includes('environment');
  });

  return backCamera?.id || cameras[0]?.id || '';
};

const BarcodeCameraScanner = ({
  readerId,
  onScan,
  active = true,
  scanDelay = 1500,
  panelClassName = '',
}) => {
  const [cameras, setCameras] = useState([]);
  const [selectedCameraId, setSelectedCameraId] = useState('');
  const [statusText, setStatusText] = useState('Memuat kamera...');
  const [errorText, setErrorText] = useState('');
  const [isScanning, setIsScanning] = useState(false);

  const scannerRef = useRef(null);
  const scanLockRef = useRef(false);
  const onScanRef = useRef(onScan);

  useEffect(() => {
    onScanRef.current = onScan;
  }, [onScan]);

  const stopScanner = useCallback(async () => {
    const scanner = scannerRef.current;

    if (!scanner) return;

    try {
      if (scanner.isScanning) {
        await scanner.stop();
      }

      scanner.clear();
    } catch (error) {
      // Scanner cleanup can fail if the camera was already stopped by the browser.
    } finally {
      scannerRef.current = null;
      setIsScanning(false);
    }
  }, []);

  const loadCameras = useCallback(async () => {
    setErrorText('');
    setStatusText('Meminta akses kamera...');

    try {
      const devices = await Html5Qrcode.getCameras();

      if (!devices.length) {
        setCameras([]);
        setSelectedCameraId('');
        setStatusText('Tidak ada kamera terdeteksi.');
        return;
      }

      setCameras(devices);
      setSelectedCameraId((current) => (
        devices.some((camera) => camera.id === current)
          ? current
          : getPreferredCameraId(devices)
      ));
      setStatusText('Pilih kamera untuk mulai scan.');
    } catch (error) {
      setErrorText('Kamera tidak bisa diakses. Izinkan akses kamera dari browser, lalu coba lagi.');
      setStatusText('Akses kamera belum tersedia.');
    }
  }, []);

  useEffect(() => {
    loadCameras();

    return () => {
      stopScanner();
    };
  }, [loadCameras, stopScanner]);

  useEffect(() => {
    if (!active || !selectedCameraId) {
      stopScanner();
      return undefined;
    }

    let cancelled = false;

    const startScanner = async () => {
      await stopScanner();

      if (cancelled) return;

      const scanner = new Html5Qrcode(readerId, {
        formatsToSupport: SUPPORTED_BARCODE_FORMATS,
        useBarCodeDetectorIfSupported: true,
        verbose: false,
      });
      scannerRef.current = scanner;
      setErrorText('');
      setStatusText('Menyalakan kamera...');

      try {
        await scanner.start(
          selectedCameraId,
          {
            fps: 15,
            qrbox: (viewfinderWidth, viewfinderHeight) => {
              const safeWidth = Math.max(1, Math.floor(viewfinderWidth));
              const safeHeight = Math.max(1, Math.floor(viewfinderHeight));
              const width = Math.max(1, Math.floor(Math.min(safeWidth * 0.86, safeHeight * 1.9, 520)));
              const height = Math.max(1, Math.floor(Math.min(safeHeight * 0.72, width * 0.42)));

              return { width, height };
            },
            disableFlip: false,
          },
          (decodedText) => {
            if (scanLockRef.current) return;

            scanLockRef.current = true;
            const scannedValue = String(decodedText || '').trim();
            setStatusText(`Barcode terbaca: ${scannedValue}`);
            onScanRef.current?.(scannedValue);
            setTimeout(() => {
              scanLockRef.current = false;
            }, scanDelay);
          },
          () => {}
        );

        if (!cancelled) {
          setIsScanning(true);
          setStatusText('Kamera aktif. Arahkan seluruh garis barcode ke area scan.');
        }
      } catch (error) {
        setIsScanning(false);
        setErrorText('Kamera gagal dinyalakan. Pilih kamera lain atau cek izin browser.');
        setStatusText('Scanner berhenti.');
      }
    };

    startScanner();

    return () => {
      cancelled = true;
      stopScanner();
    };
  }, [active, readerId, scanDelay, selectedCameraId, stopScanner]);

  return (
    <div className={panelClassName}>
      <div className="mb-3">
        <label className="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">
          Kamera Scanner
        </label>
        <div className="flex gap-2">
          <select
            value={selectedCameraId}
            onChange={(event) => setSelectedCameraId(event.target.value)}
            disabled={!active || !cameras.length}
            className="min-w-0 flex-1 p-3 border rounded-xl bg-white text-sm font-bold outline-none focus:ring-2 focus:ring-[#265F9C] disabled:bg-gray-100"
          >
            {!cameras.length && <option value="">Tidak ada kamera</option>}
            {cameras.map((camera, index) => (
              <option key={camera.id} value={camera.id}>
                {camera.label || `Kamera ${index + 1}`}
              </option>
            ))}
          </select>
          <button
            type="button"
            onClick={loadCameras}
            className="px-4 py-3 bg-gray-100 border rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors"
          >
            Refresh
          </button>
        </div>
      </div>

      <div className="relative rounded-2xl border border-slate-300 bg-slate-50 p-3 shadow-inner">
        <div
          id={readerId}
          className="barcode-camera-reader overflow-hidden rounded-xl border-2 border-dashed border-[#265F9C]/50 bg-white min-h-[260px]"
        />
        <div className="pointer-events-none absolute inset-6 rounded-lg border border-[#265F9C]/30" />
        <div className="pointer-events-none absolute left-1/2 top-1/2 h-[2px] w-[72%] -translate-x-1/2 -translate-y-1/2 bg-[#265F9C]/50 shadow-sm" />
      </div>

      <p className={`mt-2 text-center text-sm font-bold ${errorText ? 'text-red-700' : 'text-gray-500'}`}>
        {errorText || statusText}
      </p>
      {isScanning && (
        <p className="mt-1 text-center text-[11px] uppercase tracking-widest text-gray-400">
          Preview kamera aktif
        </p>
      )}
    </div>
  );
};

export default BarcodeCameraScanner;
