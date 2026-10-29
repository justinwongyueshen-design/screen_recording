<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OmniCapture - Web Screen Recorder & Studio</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Google Fonts Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#f0f3ff',
              100: '#e1e7fe',
              500: '#6366f1',
              600: '#4f46e5',
              700: '#4338ca',
              800: '#3730a3',
              900: '#312e81',
              dark: '#0B0F19',
              card: '#111827',
              accent: '#8b5cf6'
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <style>
    body {
      background-color: #0b0f19;
      color: #f3f4f6;
      font-family: 'Inter', sans-serif;
      overflow-x: hidden;
    }
    
    /* Glassmorphism Styles */
    .glass-panel {
      background: rgba(17, 24, 39, 0.75);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    }

    .glass-card {
      background: rgba(30, 41, 59, 0.5);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .glass-button {
      background: rgba(99, 102, 241, 0.2);
      border: 1px solid rgba(129, 140, 248, 0.3);
      backdrop-filter: blur(8px);
      transition: all 0.2s ease;
    }
    .glass-button:hover:not(:disabled) {
      background: rgba(99, 102, 241, 0.4);
      border-color: rgba(129, 140, 248, 0.6);
      transform: translateY(-1px);
    }
    .glass-button:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: rgba(15, 23, 42, 0.6);
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(99, 102, 241, 0.4);
      border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: rgba(99, 102, 241, 0.7);
    }

    /* Pulse animation for recording badge */
    @keyframes pulse-ring {
      0% { transform: scale(0.95); opacity: 0.8; }
      50% { transform: scale(1.15); opacity: 0.4; }
      100% { transform: scale(0.95); opacity: 0.8; }
    }
    .rec-pulse {
      animation: pulse-ring 2s infinite ease-in-out;
    }

    /* Webcam PIP overlay handle styles */
    #webcamPIP {
      touch-action: none;
      user-select: none;
    }
    .resize-handle {
      position: absolute;
      width: 12px;
      height: 12px;
      background: #8b5cf6;
      border: 2px solid #ffffff;
      border-radius: 50%;
      bottom: -4px;
      right: -4px;
      cursor: nwse-resize;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-brand-500 selection:text-white">

  <!-- TOP HEADER NAVBAR -->
  <header class="sticky top-0 z-40 w-full glass-panel border-b border-gray-800 px-4 lg:px-8 py-3">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <!-- Logo -->
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
          <i class="fa-solid font-bold fa-video text-white text-lg"></i>
        </div>
        <div>
          <h1 class="font-extrabold text-lg tracking-wide text-white flex items-center gap-2">
            OmniCapture <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">PRO</span>
          </h1>
          <p class="text-xs text-gray-400">Web Screen Recorder & Studio</p>
        </div>
      </div>

      <!-- Live Status & Rec Timer -->
      <div class="flex items-center space-x-4">
        <div id="statusIndicator" class="hidden items-center gap-2 px-3 py-1.5 rounded-full bg-gray-800/80 border border-gray-700 text-xs font-medium">
          <span id="statusDot" class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
          <span id="statusText" class="text-gray-300">Ready</span>
        </div>

        <div id="timerContainer" class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900/90 border border-indigo-500/30 font-mono text-sm text-indigo-300 font-bold shadow-inner">
          <i class="fa-regular fa-clock text-indigo-400 text-xs"></i>
          <span id="recordingTimer">00:00:00</span>
        </div>
      </div>

      <!-- Action Buttons Right Top -->
      <div class="flex items-center space-x-2">
        <button id="btnShortcuts" title="Keyboard Shortcuts" class="p-2.5 rounded-xl glass-button text-gray-300 hover:text-white">
          <i class="fa-solid fa-keyboard text-sm"></i>
        </button>
        <button id="btnLibrary" title="Open Library" class="p-2.5 rounded-xl glass-button text-gray-300 hover:text-white flex items-center gap-2">
          <i class="fa-solid fa-folder-open text-sm"></i>
          <span class="hidden sm:inline text-xs font-medium">Library</span>
          <span id="libraryBadge" class="bg-indigo-600 text-white text-[10px] px-1.5 py-0.2 rounded-full font-semibold">0</span>
        </button>
        <button id="btnSettings" title="Settings" class="p-2.5 rounded-xl glass-button text-gray-300 hover:text-white">
          <i class="fa-solid fa-gear text-sm"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- MAIN STUDIO CONTENT WORKSPACE -->
  <main class="flex-1 max-w-7xl w-full mx-auto p-4 lg:p-6 flex flex-col gap-6">

    <!-- MAIN STUDIO STAGE & PREVIEW CONTAINER -->
    <div class="relative w-full aspect-video bg-gray-950 rounded-2xl border border-gray-800/80 overflow-hidden shadow-2xl flex flex-col justify-center items-center group">
      
      <!-- Offscreen or Hidden Source Streams -->
      <video id="screenVideo" autoplay playsinline muted class="hidden"></video>
      <video id="webcamVideo" autoplay playsinline muted class="hidden"></video>
      
      <!-- Studio Composite Live Canvas -->
      <canvas id="studioCanvas" class="w-full h-full object-contain bg-black"></canvas>

      <!-- Placeholder Screen Selection Prompt (if no screen selected) -->
      <div id="noSourceOverlay" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-slate-950/90 z-10 transition-opacity duration-300">
        <div class="w-20 h-20 rounded-2xl bg-indigo-950/60 border border-indigo-500/30 flex items-center justify-center text-indigo-400 mb-4 shadow-lg shadow-indigo-950/50">
          <i class="fa-solid fa-desktop text-3xl"></i>
        </div>
        <h2 class="text-xl font-bold text-white mb-2">No Screen Source Selected</h2>
        <p class="text-gray-400 text-sm max-w-md mb-6">Select a display monitor, window, or browser tab to start live preview and studio recording.</p>
        <div class="flex flex-wrap items-center justify-center gap-3">
          <button id="btnSelectScreen" class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
            <i class="fa-solid fa-display"></i>
            Select Display Source
          </button>
          <button id="btnOpenWindow" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            Open in Dedicated Tab
          </button>
        </div>
        <div id="permissionNotice" class="max-w-md mt-4 p-3 bg-amber-950/50 border border-amber-500/40 rounded-xl text-left text-xs text-amber-200">
          <div class="flex items-start gap-2.5">
            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-base mt-0.5 shrink-0"></i>
            <div>
              <p class="font-semibold text-amber-300 mb-0.5">Iframe Permissions Policy Restriction</p>
              <p class="text-amber-200/90 leading-relaxed">Browsers block screen recording inside embedded preview frames. Click <strong class="text-white underline">"Open in Dedicated Tab"</strong> to launch OmniCapture in its own window where screen recording permissions are fully allowed.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- WEBCAM PIP DRAGGABLE OVERLAY (ON STAGE INTERACTION) -->
      <div id="webcamPIP" class="hidden absolute top-6 left-6 z-20 cursor-move border-2 border-indigo-500 shadow-2xl group/pip transition-all duration-75 overflow-hidden" style="width: 180px; height: 135px; border-radius: 12px;">
        <canvas id="pipCanvas" class="w-full h-full object-cover pointer-events-none"></canvas>
        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover/pip:opacity-100 transition-opacity flex items-center justify-center gap-1.5 p-1">
          <button id="btnPipShape" title="Toggle Shape (Circle/Rect)" class="p-1.5 bg-gray-900/80 hover:bg-black rounded text-xs text-white">
            <i class="fa-solid fa-shapes"></i>
          </button>
          <button id="btnPipMirror" title="Flip Horizontal" class="p-1.5 bg-gray-900/80 hover:bg-black rounded text-xs text-white">
            <i class="fa-solid fa-arrows-rotate"></i>
          </button>
        </div>
        <div class="resize-handle" id="pipResizeHandle"></div>
      </div>

      <!-- STREAM METADATA OVERLAY (TOP LEFT OVER CANVAS) -->
      <div class="absolute top-4 left-4 z-10 hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/60 backdrop-blur-md border border-white/10 text-xs text-gray-300">
        <span class="flex items-center gap-1.5">
          <i class="fa-solid fa-expand text-indigo-400"></i>
          <span id="metaResolution">1920x1080</span>
        </span>
        <span class="text-gray-600">|</span>
        <span class="flex items-center gap-1.5">
          <i class="fa-solid fa-gauge-high text-indigo-400"></i>
          <span id="metaFPS">60 FPS</span>
        </span>
      </div>

      <!-- FLOATING STUDIO CONTROL BAR (BOTTOM CENTER OVER CANVAS) -->
      <div class="absolute bottom-6 z-30 px-6 py-3 rounded-2xl glass-panel flex items-center gap-3 sm:gap-5 shadow-2xl border border-white/10">
        
        <!-- Screen Source Selector Toggle -->
        <button id="btnToggleScreen" class="flex flex-col items-center gap-1 text-gray-300 hover:text-white group">
          <div class="w-10 h-10 rounded-xl bg-gray-800/80 group-hover:bg-indigo-600/30 flex items-center justify-center border border-gray-700 transition">
            <i class="fa-solid fa-desktop text-sm"></i>
          </div>
          <span class="text-[10px] font-medium text-gray-400">Screen</span>
        </button>

        <!-- Webcam Toggle -->
        <button id="btnToggleWebcam" class="flex flex-col items-center gap-1 text-gray-300 hover:text-white group">
          <div id="camIconContainer" class="w-10 h-10 rounded-xl bg-gray-800/80 group-hover:bg-indigo-600/30 flex items-center justify-center border border-gray-700 transition">
            <i id="camIcon" class="fa-solid fa-video-slash text-sm text-gray-400"></i>
          </div>
          <span class="text-[10px] font-medium text-gray-400">Webcam</span>
        </button>

        <!-- Mic Toggle -->
        <button id="btnToggleMic" class="flex flex-col items-center gap-1 text-gray-300 hover:text-white group">
          <div id="micIconContainer" class="w-10 h-10 rounded-xl bg-gray-800/80 group-hover:bg-indigo-600/30 flex items-center justify-center border border-gray-700 transition">
            <i id="micIcon" class="fa-solid fa-microphone-slash text-sm text-gray-400"></i>
          </div>
          <span class="text-[10px] font-medium text-gray-400">Mic</span>
        </button>

        <div class="h-8 w-px bg-gray-800 my-auto"></div>

        <!-- MAIN RECORD START / STOP BUTTON -->
        <button id="btnRecord" disabled class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-bold text-sm shadow-lg shadow-red-600/30 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-2">
          <span class="w-3 h-3 rounded-full bg-white animate-pulse"></span>
          <span id="btnRecordText">Start Record</span>
        </button>

        <!-- PAUSE / RESUME BUTTON -->
        <button id="btnPause" disabled class="w-10 h-10 rounded-xl bg-gray-800/80 hover:bg-gray-700 text-gray-300 hover:text-white flex items-center justify-center border border-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition" title="Pause / Resume">
          <i id="pauseIcon" class="fa-solid fa-pause text-sm"></i>
        </button>

      </div>
    </div>

    <!-- AUDIO MIXER & STUDIO CONTROL PANEL -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Microphone Audio Card -->
      <div class="glass-card rounded-2xl p-5 border border-gray-800 flex flex-col gap-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
              <i class="fa-solid fa-microphone"></i>
            </div>
            <div>
              <h3 class="font-semibold text-sm text-white">Microphone Input</h3>
              <p class="text-xs text-gray-400">Voice & Speech Recording</p>
            </div>
          </div>
          <span id="micStatusText" class="text-xs px-2.5 py-0.5 rounded-full bg-gray-800 text-gray-400 font-medium">Off</span>
        </div>

        <!-- Audio Meter -->
        <div class="space-y-1">
          <div class="flex justify-between text-xs text-gray-400">
            <span>Input Level</span>
            <span id="micMeterValue">0%</span>
          </div>
          <div class="w-full h-2.5 bg-gray-900 rounded-full overflow-hidden p-0.5 border border-gray-800">
            <div id="micMeterBar" class="h-full bg-gradient-to-r from-emerald-500 via-yellow-500 to-red-500 rounded-full transition-all duration-75" style="width: 0%"></div>
          </div>
        </div>

        <!-- Volume Slider -->
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-volume-low text-xs text-gray-500"></i>
          <input id="micVolumeSlider" type="range" min="0" max="2" step="0.05" value="1" class="flex-1 h-1.5 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-purple-500" />
          <i class="fa-solid fa-volume-high text-xs text-gray-400"></i>
        </div>
      </div>

      <!-- System Audio Card -->
      <div class="glass-card rounded-2xl p-5 border border-gray-800 flex flex-col gap-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
              <i class="fa-solid fa-volume-high"></i>
            </div>
            <div>
              <h3 class="font-semibold text-sm text-white">System / Tab Audio</h3>
              <p class="text-xs text-gray-400">Internal System & Browser Sound</p>
            </div>
          </div>
          <span id="sysStatusText" class="text-xs px-2.5 py-0.5 rounded-full bg-gray-800 text-gray-400 font-medium">Off</span>
        </div>

        <!-- Audio Meter -->
        <div class="space-y-1">
          <div class="flex justify-between text-xs text-gray-400">
            <span>Input Level</span>
            <span id="sysMeterValue">0%</span>
          </div>
          <div class="w-full h-2.5 bg-gray-900 rounded-full overflow-hidden p-0.5 border border-gray-800">
            <div id="sysMeterBar" class="h-full bg-gradient-to-r from-emerald-500 via-indigo-500 to-purple-500 rounded-full transition-all duration-75" style="width: 0%"></div>
          </div>
        </div>

        <!-- Volume Slider -->
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-volume-low text-xs text-gray-500"></i>
          <input id="sysVolumeSlider" type="range" min="0" max="2" step="0.05" value="1" class="flex-1 h-1.5 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-indigo-500" />
          <i class="fa-solid fa-volume-high text-xs text-gray-400"></i>
        </div>
      </div>

    </div>
  </main>

  <!-- FOOTER -->
  <footer class="w-full border-t border-gray-800/60 py-4 px-6 text-center text-xs text-gray-500">
    <p>OmniCapture Pro Screen Recorder &bull; Secure Local Processing &bull; No Server Uploads</p>
  </footer>

  <!-- MODAL: SETTINGS -->
  <div id="settingsModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-lg rounded-2xl border border-gray-800 p-6 flex flex-col gap-5 shadow-2xl">
      <div class="flex items-center justify-between border-b border-gray-800 pb-3">
        <h3 class="font-bold text-lg text-white flex items-center gap-2">
          <i class="fa-solid fa-sliders text-indigo-400"></i> Recording Configuration
        </h3>
        <button id="btnCloseSettings" class="text-gray-400 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div class="space-y-4">
        <!-- Target Resolution -->
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1">Target Video Resolution</label>
          <select id="cfgResolution" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            <option value="1080p">1080p Full HD (1920x1080)</option>
            <option value="720p">720p HD (1280x720)</option>
            <option value="4k">4K Ultra HD (3840x2160)</option>
          </select>
        </div>

        <!-- Frame Rate -->
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1">Frame Rate (FPS)</label>
          <select id="cfgFps" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            <option value="60">60 FPS (High Smoothness)</option>
            <option value="30">30 FPS (Standard)</option>
          </select>
        </div>

        <!-- Video Bitrate -->
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1">Video Bitrate</label>
          <select id="cfgVideoBitrate" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-indigo-500">
            <option value="8000000">8 Mbps (High Quality)</option>
            <option value="5000000">5 Mbps (Recommended)</option>
            <option value="2500000">2.5 Mbps (Balanced)</option>
          </select>
        </div>

        <!-- Audio Enhancements -->
        <div class="space-y-2 pt-2 border-t border-gray-800">
          <label class="block text-xs font-semibold text-gray-300">Audio Processing Controls</label>
          <div class="flex items-center justify-between text-xs text-gray-300">
            <span>Echo Cancellation</span>
            <input id="cfgEcho" type="checkbox" checked class="w-4 h-4 accent-indigo-500 rounded" />
          </div>
          <div class="flex items-center justify-between text-xs text-gray-300">
            <span>Noise Suppression</span>
            <input id="cfgNoise" type="checkbox" checked class="w-4 h-4 accent-indigo-500 rounded" />
          </div>
        </div>

        <!-- Default Filename -->
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1">Default Filename Prefix</label>
          <input id="cfgFilename" type="text" value="OmniCapture_Record" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-indigo-500" />
        </div>
      </div>

      <div class="flex justify-end gap-2 pt-2 border-t border-gray-800">
        <button id="btnSaveSettings" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold transition">Save Settings</button>
      </div>
    </div>
  </div>

  <!-- MODAL: PREVIEW & EXPORT -->
  <div id="previewModal" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-4xl rounded-2xl border border-gray-800 p-6 flex flex-col gap-4 shadow-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-gray-800 pb-3">
        <h3 class="font-bold text-lg text-white flex items-center gap-2">
          <i class="fa-solid fa-circle-play text-indigo-400"></i> Recording Preview & Export
        </h3>
        <button id="btnClosePreview" class="text-gray-400 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <!-- Preview Video Player -->
      <div class="w-full aspect-video bg-black rounded-xl overflow-hidden border border-gray-800 relative">
        <video id="previewPlayer" controls class="w-full h-full object-contain"></video>
      </div>

      <!-- Info Details -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-900/60 p-3 rounded-xl border border-gray-800 text-xs text-gray-300">
        <div><span class="text-gray-500 block">Duration</span> <span id="prevDuration" class="font-mono font-semibold">00:00</span></div>
        <div><span class="text-gray-500 block">File Size</span> <span id="prevSize" class="font-semibold">0 MB</span></div>
        <div><span class="text-gray-500 block">Container Codec</span> <span id="prevCodec" class="font-semibold">video/webm</span></div>
        <div><span class="text-gray-500 block">Resolution</span> <span id="prevRes" class="font-semibold">1080p</span></div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap items-center justify-end gap-3 pt-2 border-t border-gray-800">
        <button id="btnSnapshot" class="px-4 py-2.5 rounded-xl glass-button text-gray-200 text-xs font-semibold flex items-center gap-2">
          <i class="fa-solid fa-camera"></i> Capture PNG Snapshot
        </button>
        <button id="btnSaveDB" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-semibold flex items-center gap-2 transition">
          <i class="fa-solid fa-box-archive"></i> Save to Local Library
        </button>
        <button id="btnDownload" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-semibold flex items-center gap-2 shadow-lg shadow-indigo-600/30 transition">
          <i class="fa-solid fa-download"></i> Download Video
        </button>
      </div>
    </div>
  </div>

  <!-- MODAL: RECORDINGS LIBRARY -->
  <div id="libraryModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-5xl rounded-2xl border border-gray-800 p-6 flex flex-col gap-4 shadow-2xl max-h-[85vh]">
      <div class="flex items-center justify-between border-b border-gray-800 pb-3">
        <div>
          <h3 class="font-bold text-lg text-white flex items-center gap-2">
            <i class="fa-solid fa-box-archive text-indigo-400"></i> Saved Recordings Library
          </h3>
          <p class="text-xs text-gray-400">Stored locally in IndexedDB</p>
        </div>
        <button id="btnCloseLibrary" class="text-gray-400 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <!-- Library Grid -->
      <div id="libraryContainer" class="flex-1 overflow-y-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-1">
        <!-- Empty state default -->
        <div class="col-span-full flex flex-col items-center justify-center py-12 text-center text-gray-500">
          <i class="fa-solid fa-folder-open text-4xl mb-2 text-gray-600"></i>
          <p class="text-sm">No saved recordings in library.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: KEYBOARD SHORTCUTS HELP -->
  <div id="shortcutsModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel w-full max-w-md rounded-2xl border border-gray-800 p-6 flex flex-col gap-4 shadow-2xl">
      <div class="flex items-center justify-between border-b border-gray-800 pb-3">
        <h3 class="font-bold text-base text-white flex items-center gap-2">
          <i class="fa-solid fa-keyboard text-indigo-400"></i> Keyboard Shortcuts
        </h3>
        <button id="btnCloseShortcuts" class="text-gray-400 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div class="space-y-3 text-xs text-gray-300">
        <div class="flex items-center justify-between p-2 rounded bg-gray-900 border border-gray-800">
          <span>Start / Stop Recording</span>
          <kbd class="px-2 py-1 bg-gray-800 rounded border border-gray-700 font-mono text-indigo-300">Alt + R</kbd>
        </div>
        <div class="flex items-center justify-between p-2 rounded bg-gray-900 border border-gray-800">
          <span>Pause / Resume</span>
          <kbd class="px-2 py-1 bg-gray-800 rounded border border-gray-700 font-mono text-indigo-300">Alt + P</kbd>
        </div>
        <div class="flex items-center justify-between p-2 rounded bg-gray-900 border border-gray-800">
          <span>Toggle Webcam</span>
          <kbd class="px-2 py-1 bg-gray-800 rounded border border-gray-700 font-mono text-indigo-300">Alt + C</kbd>
        </div>
        <div class="flex items-center justify-between p-2 rounded bg-gray-900 border border-gray-800">
          <span>Toggle Microphone</span>
          <kbd class="px-2 py-1 bg-gray-800 rounded border border-gray-700 font-mono text-indigo-300">Alt + M</kbd>
        </div>
      </div>
    </div>
  </div>

  <!-- TOAST NOTIFICATION CONTAINER -->
  <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

  <!-- APPLICATION ARCHITECTURE JAVASCRIPT LOGIC -->
  <script>
    /**
     * OmniCapture Pro Application Core
     */

    // --- UTILITY & TOAST SYSTEM ---
    function showToast(message, type = 'info', duration = 3500) {
      const container = document.getElementById('toastContainer');
      const toast = document.createElement('div');
      toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl border shadow-xl backdrop-blur-md text-xs font-semibold transition-all duration-300 transform translate-y-2 opacity-0`;
      
      let icon = 'fa-circle-info text-blue-400';
      let border = 'border-blue-500/30 bg-slate-900/90 text-white';

      if (type === 'success') {
        icon = 'fa-circle-check text-emerald-400';
        border = 'border-emerald-500/30 bg-slate-900/90 text-white';
      } else if (type === 'error') {
        icon = 'fa-circle-exclamation text-rose-400';
        border = 'border-rose-500/30 bg-slate-900/90 text-white';
      } else if (type === 'warning') {
        icon = 'fa-triangle-exclamation text-amber-400';
        border = 'border-amber-500/30 bg-slate-900/90 text-white';
      }

      toast.className += ` ${border}`;
      toast.innerHTML = `<i class="fa-solid ${icon} text-sm"></i> <span>${escapeHtml(message)}</span>`;

      container.appendChild(toast);
      requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
      });

      setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
      }, duration);
    }

    function escapeHtml(str) {
      return str.replace(/[&<>"']/g, (m) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
      })[m]);
    }

    function formatBytes(bytes, decimals = 2) {
      if (!bytes || bytes === 0) return '0 Bytes';
      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function formatTime(seconds) {
      const hrs = Math.floor(seconds / 3600);
      const mins = Math.floor((seconds % 3600) / 60);
      const secs = Math.floor(seconds % 60);
      return [hrs, mins, secs].map(v => v < 10 ? "0" + v : v).join(":");
    }

    // --- INDEXEDDB STORAGE SYSTEM ---
    class StorageDB {
      constructor() {
        this.dbName = 'OmniCaptureDB';
        this.dbVersion = 1;
        this.db = null;
      }

      async init() {
        return new Promise((resolve, reject) => {
          const request = indexedDB.open(this.dbName, this.dbVersion);
          request.onerror = (e) => reject('Database error: ' + e.target.errorCode);
          request.onsuccess = (e) => {
            this.db = e.target.result;
            resolve(this.db);
          };
          request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('recordings')) {
              db.createObjectStore('recordings', { keyPath: 'id' });
            }
          };
        });
      }

      async saveRecord(record) {
        return new Promise((resolve, reject) => {
          const tx = this.db.transaction(['recordings'], 'readwrite');
          const store = tx.objectStore('recordings');
          const request = store.put(record);
          request.onsuccess = () => resolve(true);
          request.onerror = (e) => reject(e);
        });
      }

      async getAllRecords() {
        return new Promise((resolve, reject) => {
          const tx = this.db.transaction(['recordings'], 'readonly');
          const store = tx.objectStore('recordings');
          const request = store.getAll();
          request.onsuccess = () => resolve(request.result || []);
          request.onerror = (e) => reject(e);
        });
      }

      async deleteRecord(id) {
        return new Promise((resolve, reject) => {
          const tx = this.db.transaction(['recordings'], 'readwrite');
          const store = tx.objectStore('recordings');
          const request = store.delete(id);
          request.onsuccess = () => resolve(true);
          request.onerror = (e) => reject(e);
        });
      }
    }

    const storage = new StorageDB();

    // --- AUDIO MIXER ENGINE ---
    class AudioMixerEngine {
      constructor() {
        this.audioCtx = null;
        this.destination = null;
        this.micSource = null;
        this.micGain = null;
        this.micAnalyser = null;
        this.sysSource = null;
        this.sysGain = null;
        this.sysAnalyser = null;
      }

      init() {
        if (!this.audioCtx) {
          const AudioContext = window.AudioContext || window.webkitAudioContext;
          this.audioCtx = new AudioContext();
          this.destination = this.audioCtx.createMediaStreamDestination();
        }
        if (this.audioCtx.state === 'suspended') {
          this.audioCtx.resume();
        }
      }

      attachMicStream(stream) {
        this.init();
        if (this.micSource) {
          try { this.micSource.disconnect(); } catch(e){}
        }
        this.micSource = this.audioCtx.createMediaStreamSource(stream);
        this.micGain = this.audioCtx.createGain();
        this.micAnalyser = this.audioCtx.createAnalyser();
        this.micAnalyser.fftSize = 64;

        this.micSource.connect(this.micGain);
        this.micGain.connect(this.micAnalyser);
        this.micGain.connect(this.destination);
      }

      attachSysStream(stream) {
        this.init();
        if (this.sysSource) {
          try { this.sysSource.disconnect(); } catch(e){}
        }
        const audioTracks = stream.getAudioTracks();
        if (audioTracks.length === 0) return;

        const sysAudioStream = new MediaStream([audioTracks[0]]);
        this.sysSource = this.audioCtx.createMediaStreamSource(sysAudioStream);
        this.sysGain = this.audioCtx.createGain();
        this.sysAnalyser = this.audioCtx.createAnalyser();
        this.sysAnalyser.fftSize = 64;

        this.sysSource.connect(this.sysGain);
        this.sysGain.connect(this.sysAnalyser);
        this.sysGain.connect(this.destination);
      }

      setMicVolume(val) {
        if (this.micGain) this.micGain.gain.value = val;
      }

      setSysVolume(val) {
        if (this.sysGain) this.sysGain.gain.value = val;
      }

      getMicLevel() {
        if (!this.micAnalyser) return 0;
        const data = new Uint8Array(this.micAnalyser.frequencyBinCount);
        this.micAnalyser.getByteFrequencyData(data);
        let sum = 0;
        for (let i = 0; i < data.length; i++) sum += data[i];
        return Math.min(100, Math.round((sum / data.length) / 128 * 100));
      }

      getSysLevel() {
        if (!this.sysAnalyser) return 0;
        const data = new Uint8Array(this.sysAnalyser.frequencyBinCount);
        this.sysAnalyser.getByteFrequencyData(data);
        let sum = 0;
        for (let i = 0; i < data.length; i++) sum += data[i];
        return Math.min(100, Math.round((sum / data.length) / 128 * 100));
      }

      getMixedAudioTracks() {
        return this.destination ? this.destination.stream.getAudioTracks() : [];
      }

      cleanup() {
        if (this.audioCtx) {
          this.audioCtx.close();
          this.audioCtx = null;
        }
      }
    }

    const audioMixer = new AudioMixerEngine();

    // --- MAIN STUDIO APPLICATION STATE ---
    const appState = {
      screenStream: null,
      webcamStream: null,
      micStream: null,
      mediaRecorder: null,
      recordedChunks: [],
      isRecording: false,
      isPaused: false,
      timerInterval: null,
      recordingSeconds: 0,
      
      // PIP Webcam Properties
      pipPos: { x: 24, y: 24 },
      pipSize: { w: 180, h: 135 },
      pipShape: 'rect', // 'rect' or 'circle'
      pipMirrored: true,

      // App Config Settings
      settings: {
        resolution: '1080p',
        fps: 60,
        videoBitrate: 5000000,
        echoCancellation: true,
        noiseSuppression: true,
        filenamePrefix: 'OmniCapture_Record'
      },

      currentBlob: null,
      currentBlobUrl: null
    };

    // --- DOM ELEMENT REFERENCES ---
    const elements = {
      screenVideo: document.getElementById('screenVideo'),
      webcamVideo: document.getElementById('webcamVideo'),
      studioCanvas: document.getElementById('studioCanvas'),
      pipCanvas: document.getElementById('pipCanvas'),
      webcamPIP: document.getElementById('webcamPIP'),
      pipResizeHandle: document.getElementById('pipResizeHandle'),
      noSourceOverlay: document.getElementById('noSourceOverlay'),
      
      // Buttons
      btnSelectScreen: document.getElementById('btnSelectScreen'),
      btnToggleScreen: document.getElementById('btnToggleScreen'),
      btnToggleWebcam: document.getElementById('btnToggleWebcam'),
      btnToggleMic: document.getElementById('btnToggleMic'),
      btnRecord: document.getElementById('btnRecord'),
      btnRecordText: document.getElementById('btnRecordText'),
      btnPause: document.getElementById('btnPause'),
      pauseIcon: document.getElementById('pauseIcon'),
      btnPipShape: document.getElementById('btnPipShape'),
      btnPipMirror: document.getElementById('btnPipMirror'),
      
      // Top Bar & Controls
      timerContainer: document.getElementById('timerContainer'),
      recordingTimer: document.getElementById('recordingTimer'),
      statusIndicator: document.getElementById('statusIndicator'),
      statusDot: document.getElementById('statusDot'),
      statusText: document.getElementById('statusText'),
      metaResolution: document.getElementById('metaResolution'),
      metaFPS: document.getElementById('metaFPS'),
      camIcon: document.getElementById('camIcon'),
      micIcon: document.getElementById('micIcon'),

      // Audio Sliders & Meters
      micVolumeSlider: document.getElementById('micVolumeSlider'),
      sysVolumeSlider: document.getElementById('sysVolumeSlider'),
      micMeterBar: document.getElementById('micMeterBar'),
      sysMeterBar: document.getElementById('sysMeterBar'),
      micMeterValue: document.getElementById('micMeterValue'),
      sysMeterValue: document.getElementById('sysMeterValue'),
      micStatusText: document.getElementById('micStatusText'),
      sysStatusText: document.getElementById('sysStatusText'),

      // Modals
      btnSettings: document.getElementById('btnSettings'),
      btnCloseSettings: document.getElementById('btnCloseSettings'),
      settingsModal: document.getElementById('settingsModal'),
      btnSaveSettings: document.getElementById('btnSaveSettings'),
      btnOpenWindow: document.getElementById('btnOpenWindow'),
      permissionNotice: document.getElementById('permissionNotice'),
      
      btnLibrary: document.getElementById('btnLibrary'),
      btnCloseLibrary: document.getElementById('btnCloseLibrary'),
      libraryModal: document.getElementById('libraryModal'),
      libraryContainer: document.getElementById('libraryContainer'),
      libraryBadge: document.getElementById('libraryBadge'),

      btnShortcuts: document.getElementById('btnShortcuts'),
      btnCloseShortcuts: document.getElementById('btnCloseShortcuts'),
      shortcutsModal: document.getElementById('shortcutsModal'),

      previewModal: document.getElementById('previewModal'),
      btnClosePreview: document.getElementById('btnClosePreview'),
      previewPlayer: document.getElementById('previewPlayer'),
      prevDuration: document.getElementById('prevDuration'),
      prevSize: document.getElementById('prevSize'),
      prevCodec: document.getElementById('prevCodec'),
      prevRes: document.getElementById('prevRes'),
      btnDownload: document.getElementById('btnDownload'),
      btnSaveDB: document.getElementById('btnSaveDB'),
      btnSnapshot: document.getElementById('btnSnapshot'),

      // Config form controls
      cfgResolution: document.getElementById('cfgResolution'),
      cfgFps: document.getElementById('cfgFps'),
      cfgVideoBitrate: document.getElementById('cfgVideoBitrate'),
      cfgEcho: document.getElementById('cfgEcho'),
      cfgNoise: document.getElementById('cfgNoise'),
      cfgFilename: document.getElementById('cfgFilename')
    };

    const ctx = elements.studioCanvas.getContext('2d');
    const pipCtx = elements.pipCanvas.getContext('2d');

    // --- CANVAS COMPOSITOR & ANIMATION LOOP ---
    function getTargetDimensions() {
      switch (appState.settings.resolution) {
        case '4k': return { w: 3840, h: 2160 };
        case '720p': return { w: 1280, h: 720 };
        default: return { w: 1920, h: 1080 };
      }
    }

    function startCanvasRenderLoop() {
      const render = () => {
        const { w, h } = getTargetDimensions();
        if (elements.studioCanvas.width !== w || elements.studioCanvas.height !== h) {
          elements.studioCanvas.width = w;
          elements.studioCanvas.height = h;
        }

        // Clear canvas background
        ctx.fillStyle = '#05070d';
        ctx.fillRect(0, 0, w, h);

        // 1. Draw Screen Capture Source
        if (appState.screenStream && elements.screenVideo.readyState >= 2) {
          ctx.drawImage(elements.screenVideo, 0, 0, w, h);
        } else {
          // Placeholder inside canvas if screen missing
          ctx.fillStyle = '#111827';
          ctx.fillRect(0, 0, w, h);
          ctx.fillStyle = '#4b5563';
          ctx.font = '24px Inter, sans-serif';
          ctx.textAlign = 'center';
          ctx.fillText('Select screen source to start live canvas stream', w / 2, h / 2);
        }

        // 2. Draw Webcam PIP Overlay (Studio Mode Composition)
        if (appState.webcamStream && elements.webcamVideo.readyState >= 2) {
          const pipX = (appState.pipPos.x / elements.studioCanvas.clientWidth) * w;
          const pipY = (appState.pipPos.y / elements.studioCanvas.clientHeight) * h;
          const pipW = (appState.pipSize.w / elements.studioCanvas.clientWidth) * w;
          const pipH = (appState.pipSize.h / elements.studioCanvas.clientHeight) * h;

          ctx.save();
          if (appState.pipShape === 'circle') {
            ctx.beginPath();
            ctx.arc(pipX + pipW / 2, pipY + pipH / 2, Math.min(pipW, pipH) / 2, 0, Math.PI * 2);
            ctx.closePath();
            ctx.clip();
          } else {
            // Rounded rectangle PIP
            ctx.beginPath();
            ctx.roundRect(pipX, pipY, pipW, pipH, 16);
            ctx.clip();
          }

          if (appState.pipMirrored) {
            ctx.translate(pipX + pipW, pipY);
            ctx.scale(-1, 1);
            ctx.drawImage(elements.webcamVideo, 0, 0, pipW, pipH);
          } else {
            ctx.drawImage(elements.webcamVideo, pipX, pipY, pipW, pipH);
          }
          ctx.restore();

          // Border overlay on main composite
          ctx.save();
          ctx.strokeStyle = '#6366f1';
          ctx.lineWidth = 4;
          if (appState.pipShape === 'circle') {
            ctx.beginPath();
            ctx.arc(pipX + pipW / 2, pipY + pipH / 2, Math.min(pipW, pipH) / 2, 0, Math.PI * 2);
            ctx.stroke();
          } else {
            ctx.beginPath();
            ctx.roundRect(pipX, pipY, pipW, pipH, 16);
            ctx.stroke();
          }
          ctx.restore();

          // Update standalone interactive HTML PIP canvas element
          if (elements.pipCanvas.width !== appState.pipSize.w || elements.pipCanvas.height !== appState.pipSize.h) {
            elements.pipCanvas.width = appState.pipSize.w;
            elements.pipCanvas.height = appState.pipSize.h;
          }
          pipCtx.clearRect(0, 0, appState.pipSize.w, appState.pipSize.h);
          pipCtx.save();
          if (appState.pipMirrored) {
            pipCtx.translate(appState.pipSize.w, 0);
            pipCtx.scale(-1, 1);
          }
          pipCtx.drawImage(elements.webcamVideo, 0, 0, appState.pipSize.w, appState.pipSize.h);
          pipCtx.restore();
        }

        // Update Audio Meter Levels
        if (appState.micStream) {
          const micLvl = audioMixer.getMicLevel();
          elements.micMeterBar.style.width = micLvl + '%';
          elements.micMeterValue.textContent = micLvl + '%';
        } else {
          elements.micMeterBar.style.width = '0%';
          elements.micMeterValue.textContent = '0%';
        }

        if (appState.screenStream) {
          const sysLvl = audioMixer.getSysLevel();
          elements.sysMeterBar.style.width = sysLvl + '%';
          elements.sysMeterValue.textContent = sysLvl + '%';
        } else {
          elements.sysMeterBar.style.width = '0%';
          elements.sysMeterValue.textContent = '0%';
        }

        requestAnimationFrame(render);
      };
      render();
    }

    // --- STREAM CAPTURE ENGINE ---

    // 1. Screen Capture Media
    async function selectScreenSource() {
      // Check if running inside an iframe without proper permissions
      if (window.self !== window.top) {
        showToast('Screen capture is restricted inside embedded previews. Opening in top window...', 'warning', 5000);
      }

      if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
        showToast('Screen capture API (getDisplayMedia) is not supported in this browser', 'error');
        return;
      }

      try {
        const displayStream = await navigator.mediaDevices.getDisplayMedia({
          video: {
            frameRate: { ideal: appState.settings.fps, max: 60 }
          },
          audio: {
            echoCancellation: true,
            noiseSuppression: true,
            sampleRate: 44100
          }
        });

        if (elements.permissionNotice) {
          elements.permissionNotice.classList.add('hidden');
        }

        appState.screenStream = displayStream;
        elements.screenVideo.srcObject = displayStream;
        elements.noSourceOverlay.classList.add('opacity-0', 'pointer-events-none');

        // Handle user stopping stream via browser native bar
        displayStream.getVideoTracks()[0].onended = () => {
          stopScreenSource();
          showToast('Screen sharing stopped by user', 'warning');
        };

        // Attach system audio if available
        if (displayStream.getAudioTracks().length > 0) {
          audioMixer.attachSysStream(displayStream);
          elements.sysStatusText.textContent = 'Active';
          elements.sysStatusText.className = 'text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-medium';
        } else {
          elements.sysStatusText.textContent = 'No Audio';
          elements.sysStatusText.className = 'text-xs px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 font-medium';
        }

        // Resolution metadata
        const track = displayStream.getVideoTracks()[0];
        const settings = track.getSettings();
        elements.metaResolution.textContent = `${settings.width || 1920}x${settings.height || 1080}`;
        elements.metaFPS.textContent = `${settings.frameRate || appState.settings.fps} FPS`;

        elements.btnRecord.disabled = false;
        updateStatus('Ready', 'emerald');
        showToast('Display capture source attached successfully', 'success');

      } catch (err) {
        console.error('Screen capture error:', err);
        const isPolicyError = err.name === 'NotAllowedError' || 
                              err.message.includes('display-capture') || 
                              err.message.includes('permissions policy');

        if (isPolicyError) {
          if (elements.permissionNotice) {
            elements.permissionNotice.classList.remove('hidden');
          }
          showToast('Blocked by browser iframe policy. Click "Open in Dedicated Tab" to record screen.', 'error', 7000);
        } else {
          showToast('Failed to acquire screen stream: ' + err.message, 'error');
        }
      }
    }

    function stopScreenSource() {
      if (appState.screenStream) {
        appState.screenStream.getTracks().forEach(track => track.stop());
        appState.screenStream = null;
      }
      elements.screenVideo.srcObject = null;
      elements.noSourceOverlay.classList.remove('opacity-0', 'pointer-events-none');
      elements.btnRecord.disabled = true;
      elements.sysStatusText.textContent = 'Off';
      elements.sysStatusText.className = 'text-xs px-2.5 py-0.5 rounded-full bg-gray-800 text-gray-400 font-medium';
      updateStatus('No Source', 'gray');
    }

    // 2. Webcam Capture Media
    async function toggleWebcam() {
      if (appState.webcamStream) {
        // Turn off
        appState.webcamStream.getTracks().forEach(track => track.stop());
        appState.webcamStream = null;
        elements.webcamVideo.srcObject = null;
        elements.webcamPIP.classList.add('hidden');
        elements.camIcon.className = 'fa-solid fa-video-slash text-sm text-gray-400';
        showToast('Webcam overlay disabled', 'info');
      } else {
        try {
          const camStream = await navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: false
          });
          appState.webcamStream = camStream;
          elements.webcamVideo.srcObject = camStream;
          elements.webcamPIP.classList.remove('hidden');
          elements.camIcon.className = 'fa-solid fa-video text-sm text-indigo-400';
          showToast('Webcam PIP active', 'success');
        } catch (err) {
          console.error('Webcam error:', err);
          showToast('Webcam access denied or unavailable', 'error');
        }
      }
    }

    // 3. Microphone Capture Media
    async function toggleMicrophone() {
      if (appState.micStream) {
        // Turn off
        appState.micStream.getTracks().forEach(track => track.stop());
        appState.micStream = null;
        elements.micIcon.className = 'fa-solid fa-microphone-slash text-sm text-gray-400';
        elements.micStatusText.textContent = 'Off';
        elements.micStatusText.className = 'text-xs px-2.5 py-0.5 rounded-full bg-gray-800 text-gray-400 font-medium';
        showToast('Microphone disabled', 'info');
      } else {
        try {
          const micStream = await navigator.mediaDevices.getUserMedia({
            audio: {
              echoCancellation: appState.settings.echoCancellation,
              noiseSuppression: appState.settings.noiseSuppression
            },
            video: false
          });
          appState.micStream = micStream;
          audioMixer.attachMicStream(micStream);
          elements.micIcon.className = 'fa-solid fa-microphone text-sm text-purple-400';
          elements.micStatusText.textContent = 'Active';
          elements.micStatusText.className = 'text-xs px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-400 font-medium';
          showToast('Microphone input active', 'success');
        } catch (err) {
          console.error('Mic error:', err);
          showToast('Microphone access denied or unavailable', 'error');
        }
      }
    }

    // --- MEDIARECORDER LIFECYCLE MANAGEMENT ---
    function getSupportedMimeType() {
      const types = [
        'video/webm;codecs=vp9,opus',
        'video/webm;codecs=vp8,opus',
        'video/webm;codecs=h264,opus',
        'video/webm',
        'video/mp4'
      ];
      for (const type of types) {
        if (MediaRecorder.isTypeSupported(type)) {
          return type;
        }
      }
      return '';
    }

    function startRecording() {
      if (!appState.screenStream) {
        showToast('Please select a screen display source first', 'warning');
        return;
      }

      appState.recordedChunks = [];
      
      // Composite Stream from Canvas FPS
      const canvasStream = elements.studioCanvas.captureStream(appState.settings.fps);
      
      // Combine Canvas Video Track + Audio Mixer Tracks
      const compositeStream = new MediaStream();
      canvasStream.getVideoTracks().forEach(track => compositeStream.addTrack(track));
      
      const mixedAudioTracks = audioMixer.getMixedAudioTracks();
      mixedAudioTracks.forEach(track => compositeStream.addTrack(track));

      const mimeType = getSupportedMimeType();
      if (!mimeType) {
        showToast('No compatible MediaRecorder MIME type supported on browser', 'error');
        return;
      }

      try {
        appState.mediaRecorder = new MediaRecorder(compositeStream, {
          mimeType: mimeType,
          videoBitsPerSecond: appState.settings.videoBitrate
        });

        appState.mediaRecorder.ondataavailable = (e) => {
          if (e.data && e.data.size > 0) {
            appState.recordedChunks.push(e.data);
          }
        };

        appState.mediaRecorder.onstop = () => {
          finishRecording();
        };

        appState.mediaRecorder.start(1000); // chunk every 1 second
        appState.isRecording = true;
        appState.isPaused = false;

        // UI Updates
        elements.btnRecordText.textContent = 'Stop Record';
        elements.btnPause.disabled = false;
        elements.btnSelectScreen.disabled = true;
        updateStatus('Recording', 'red');
        startTimer();

        showToast('Studio recording started', 'success');

      } catch (err) {
        console.error('MediaRecorder start failure:', err);
        showToast('Failed to start recorder: ' + err.message, 'error');
      }
    }

    function togglePauseRecording() {
      if (!appState.mediaRecorder || !appState.isRecording) return;

      if (appState.isPaused) {
        appState.mediaRecorder.resume();
        appState.isPaused = false;
        elements.pauseIcon.className = 'fa-solid fa-pause text-sm';
        updateStatus('Recording', 'red');
        startTimer();
        showToast('Recording resumed', 'info');
      } else {
        appState.mediaRecorder.pause();
        appState.isPaused = true;
        elements.pauseIcon.className = 'fa-solid fa-play text-sm';
        updateStatus('Paused', 'amber');
        stopTimer();
        showToast('Recording paused', 'warning');
      }
    }

    function stopRecording() {
      if (appState.mediaRecorder && appState.isRecording) {
        appState.mediaRecorder.stop();
        appState.isRecording = false;
        appState.isPaused = false;
        stopTimer();
        elements.btnRecordText.textContent = 'Start Record';
        elements.btnPause.disabled = true;
        elements.pauseIcon.className = 'fa-solid fa-pause text-sm';
        elements.btnSelectScreen.disabled = false;
        updateStatus('Processing', 'purple');
      }
    }

    function finishRecording() {
      const mimeType = getSupportedMimeType();
      appState.currentBlob = new Blob(appState.recordedChunks, { type: mimeType });
      if (appState.currentBlobUrl) {
        URL.revokeObjectURL(appState.currentBlobUrl);
      }
      appState.currentBlobUrl = URL.createObjectURL(appState.currentBlob);

      // Populate Preview Modal Details
      elements.previewPlayer.src = appState.currentBlobUrl;
      elements.prevDuration.textContent = formatTime(appState.recordingSeconds);
      elements.prevSize.textContent = formatBytes(appState.currentBlob.size);
      elements.prevCodec.textContent = mimeType;
      elements.prevRes.textContent = elements.metaResolution.textContent;

      // Show Export Modal
      elements.previewModal.classList.remove('hidden');
      updateStatus('Ready', 'emerald');
      showToast('Recording processed! Ready for preview & download', 'success');
      
      // Reset Timer State
      appState.recordingSeconds = 0;
      elements.recordingTimer.textContent = '00:00:00';
    }

    // --- TIMER CONTROLLER ---
    function startTimer() {
      if (appState.timerInterval) clearInterval(appState.timerInterval);
      appState.timerInterval = setInterval(() => {
        if (!appState.isPaused) {
          appState.recordingSeconds++;
          elements.recordingTimer.textContent = formatTime(appState.recordingSeconds);
        }
      }, 1000);
    }

    function stopTimer() {
      if (appState.timerInterval) {
        clearInterval(appState.timerInterval);
        appState.timerInterval = null;
      }
    }

    function updateStatus(text, color) {
      elements.statusText.textContent = text;
      let dotColor = 'bg-gray-400';
      if (color === 'emerald') dotColor = 'bg-emerald-400 rec-pulse';
      if (color === 'red') dotColor = 'bg-red-500 rec-pulse';
      if (color === 'amber') dotColor = 'bg-amber-400';
      if (color === 'purple') dotColor = 'bg-purple-400 animate-spin';
      elements.statusDot.className = `w-2.5 h-2.5 rounded-full ${dotColor}`;
      elements.statusIndicator.classList.remove('hidden');
      elements.statusIndicator.classList.add('flex');
    }

    // --- SNAPSHOT EXPORT ---
    function captureSnapshot() {
      if (!elements.previewPlayer.src) return;
      const snapCanvas = document.createElement('canvas');
      snapCanvas.width = elements.studioCanvas.width || 1920;
      snapCanvas.height = elements.studioCanvas.height || 1080;
      const snapCtx = snapCanvas.getContext('2d');
      snapCtx.drawImage(elements.previewPlayer, 0, 0, snapCanvas.width, snapCanvas.height);

      const link = document.createElement('a');
      link.download = `${appState.settings.filenamePrefix}_snapshot_${Date.now()}.png`;
      link.href = snapCanvas.toDataURL('image/png');
      link.click();
      showToast('Snapshot saved as PNG', 'success');
    }

    // --- LIBRARY STORAGE HANDLER ---
    async function saveToLibrary() {
      if (!appState.currentBlob) return;

      // Generate video thumbnail frame
      const snapCanvas = document.createElement('canvas');
      snapCanvas.width = 320;
      snapCanvas.height = 180;
      const snapCtx = snapCanvas.getContext('2d');
      snapCtx.drawImage(elements.previewPlayer, 0, 0, 320, 180);
      const thumbnail = snapCanvas.toDataURL('image/jpeg', 0.7);

      const record = {
        id: 'rec_' + Date.now(),
        title: `${appState.settings.filenamePrefix} - ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}`,
        blob: appState.currentBlob,
        thumbnail: thumbnail,
        duration: appState.recordingSeconds,
        size: appState.currentBlob.size,
        date: new Date().toISOString()
      };

      try {
        await storage.saveRecord(record);
        showToast('Saved to local library', 'success');
        refreshLibraryUI();
      } catch (err) {
        console.error('Storage save error:', err);
        showToast('Failed to save recording to storage', 'error');
      }
    }

    async function refreshLibraryUI() {
      try {
        const records = await storage.getAllRecords();
        elements.libraryBadge.textContent = records.length;

        if (records.length === 0) {
          elements.libraryContainer.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-center text-gray-500">
              <i class="fa-solid fa-folder-open text-4xl mb-2 text-gray-600"></i>
              <p class="text-sm">No saved recordings in library.</p>
            </div>
          `;
          return;
        }

        // Sort descending by date
        records.sort((a, b) => new Date(b.date) - new Date(a.date));

        elements.libraryContainer.innerHTML = '';
        records.forEach(rec => {
          const card = document.createElement('div');
          card.className = 'glass-card rounded-xl p-3 border border-gray-800 flex flex-col gap-2 relative group';
          card.innerHTML = `
            <div class="w-full aspect-video bg-black rounded-lg overflow-hidden relative">
              <img src="${rec.thumbnail}" class="w-full h-full object-cover" />
              <span class="absolute bottom-1.5 right-1.5 bg-black/80 px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold text-gray-200">
                ${formatTime(rec.duration || 0)}
              </span>
            </div>
            <div class="flex-1">
              <h4 class="font-semibold text-xs text-white truncate max-w-[200px]" title="${escapeHtml(rec.title)}">${escapeHtml(rec.title)}</h4>
              <p class="text-[10px] text-gray-400">${formatBytes(rec.size)} &bull; ${new Date(rec.date).toLocaleDateString()}</p>
            </div>
            <div class="flex items-center justify-between gap-1 pt-1 border-t border-gray-800">
              <button data-action="play" data-id="${rec.id}" class="px-2.5 py-1 bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 rounded text-[11px] font-medium flex items-center gap-1">
                <i class="fa-solid fa-play text-[9px]"></i> Play
              </button>
              <button data-action="download" data-id="${rec.id}" class="px-2.5 py-1 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded text-[11px] font-medium flex items-center gap-1">
                <i class="fa-solid fa-download text-[9px]"></i> Save
              </button>
              <button data-action="delete" data-id="${rec.id}" class="px-2 py-1 bg-rose-500/20 hover:bg-rose-500/40 text-rose-400 rounded text-[11px] font-medium">
                <i class="fa-solid fa-trash text-[9px]"></i>
              </button>
            </div>
          `;
          elements.libraryContainer.appendChild(card);
        });

        // Event listeners for library item actions
        elements.libraryContainer.querySelectorAll('button').forEach(btn => {
          btn.addEventListener('click', async (e) => {
            const action = btn.dataset.action;
            const id = btn.dataset.id;
            const rec = records.find(r => r.id === id);
            if (!rec) return;

            if (action === 'play') {
              elements.libraryModal.classList.add('hidden');
              const url = URL.createObjectURL(rec.blob);
              elements.previewPlayer.src = url;
              elements.prevDuration.textContent = formatTime(rec.duration || 0);
              elements.prevSize.textContent = formatBytes(rec.size);
              elements.prevCodec.textContent = rec.blob.type || 'video/webm';
              elements.prevRes.textContent = 'Stored Record';
              elements.previewModal.classList.remove('hidden');
            } else if (action === 'download') {
              const url = URL.createObjectURL(rec.blob);
              const a = document.createElement('a');
              a.href = url;
              a.download = `${rec.title}.webm`;
              a.click();
              showToast('Downloading saved recording', 'success');
            } else if (action === 'delete') {
              if (confirm(`Delete "${rec.title}" permanently?`)) {
                await storage.deleteRecord(id);
                showToast('Record deleted from library', 'info');
                refreshLibraryUI();
              }
            }
          });
        });

      } catch (err) {
        console.error('Library UI error:', err);
      }
    }

    // --- PIP DRAG & RESIZE INTERACTION ---
    function setupPipInteractions() {
      let isDragging = false;
      let isResizing = false;
      let startX, startY, startW, startH, startLeft, startTop;

      const pip = elements.webcamPIP;
      const handle = elements.pipResizeHandle;

      pip.addEventListener('mousedown', (e) => {
        if (e.target === handle) return;
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = appState.pipPos.x;
        startTop = appState.pipPos.y;
        e.preventDefault();
      });

      handle.addEventListener('mousedown', (e) => {
        isResizing = true;
        startX = e.clientX;
        startY = e.clientY;
        startW = appState.pipSize.w;
        startH = appState.pipSize.h;
        e.stopPropagation();
        e.preventDefault();
      });

      window.addEventListener('mousemove', (e) => {
        if (isDragging) {
          const dx = e.clientX - startX;
          const dy = e.clientY - startY;
          appState.pipPos.x = Math.max(10, Math.min(elements.studioCanvas.clientWidth - appState.pipSize.w - 10, startLeft + dx));
          appState.pipPos.y = Math.max(10, Math.min(elements.studioCanvas.clientHeight - appState.pipSize.h - 10, startTop + dy));
          pip.style.left = `${appState.pipPos.x}px`;
          pip.style.top = `${appState.pipPos.y}px`;
        } else if (isResizing) {
          const dx = e.clientX - startX;
          const newW = Math.max(100, Math.min(400, startW + dx));
          const newH = Math.round(newW * 0.75); // Maintain 4:3 aspect
          appState.pipSize.w = newW;
          appState.pipSize.h = newH;
          pip.style.width = `${newW}px`;
          pip.style.height = `${newH}px`;
        }
      });

      window.addEventListener('mouseup', () => {
        isDragging = false;
        isResizing = false;
      });
    }

    // --- BIND EVENT LISTENERS & INITIALIZATION ---
    function bindEvents() {
      // Stream Controls
      elements.btnSelectScreen.addEventListener('click', selectScreenSource);
      if (elements.btnOpenWindow) {
        elements.btnOpenWindow.addEventListener('click', () => {
          const win = window.open(window.location.href, '_blank');
          if (!win) {
            showToast('Pop-up blocked! Please allow pop-ups for this site.', 'warning');
          }
        });
      }
      elements.btnToggleScreen.addEventListener('click', () => {
        if (appState.screenStream) {
          stopScreenSource();
          showToast('Screen capture detached', 'info');
        } else {
          selectScreenSource();
        }
      });
      elements.btnToggleWebcam.addEventListener('click', toggleWebcam);
      elements.btnToggleMic.addEventListener('click', toggleMicrophone);

      // Recorder Controls
      elements.btnRecord.addEventListener('click', () => {
        if (appState.isRecording) {
          stopRecording();
        } else {
          startRecording();
        }
      });
      elements.btnPause.addEventListener('click', togglePauseRecording);

      // Audio Volume Sliders
      elements.micVolumeSlider.addEventListener('input', (e) => {
        audioMixer.setMicVolume(parseFloat(e.target.value));
      });
      elements.sysVolumeSlider.addEventListener('input', (e) => {
        audioMixer.setSysVolume(parseFloat(e.target.value));
      });

      // PIP Toggles
      elements.btnPipShape.addEventListener('click', () => {
        appState.pipShape = appState.pipShape === 'rect' ? 'circle' : 'rect';
        showToast(`Webcam PIP shape set to ${appState.pipShape}`, 'info');
      });
      elements.btnPipMirror.addEventListener('click', () => {
        appState.pipMirrored = !appState.pipMirrored;
        showToast('Webcam mirror toggled', 'info');
      });

      // Settings Modal
      elements.btnSettings.addEventListener('click', () => {
        elements.cfgResolution.value = appState.settings.resolution;
        elements.cfgFps.value = appState.settings.fps;
        elements.cfgVideoBitrate.value = appState.settings.videoBitrate;
        elements.cfgEcho.checked = appState.settings.echoCancellation;
        elements.cfgNoise.checked = appState.settings.noiseSuppression;
        elements.cfgFilename.value = appState.settings.filenamePrefix;
        elements.settingsModal.classList.remove('hidden');
      });
      elements.btnCloseSettings.addEventListener('click', () => elements.settingsModal.classList.add('hidden'));
      elements.btnSaveSettings.addEventListener('click', () => {
        appState.settings.resolution = elements.cfgResolution.value;
        appState.settings.fps = parseInt(elements.cfgFps.value);
        appState.settings.videoBitrate = parseInt(elements.cfgVideoBitrate.value);
        appState.settings.echoCancellation = elements.cfgEcho.checked;
        appState.settings.noiseSuppression = elements.cfgNoise.checked;
        appState.settings.filenamePrefix = elements.cfgFilename.value || 'OmniCapture_Record';
        elements.settingsModal.classList.add('hidden');
        showToast('Configuration saved', 'success');
      });

      // Export / Preview Modal
      elements.btnClosePreview.addEventListener('click', () => elements.previewModal.classList.add('hidden'));
      elements.btnDownload.addEventListener('click', () => {
        if (!appState.currentBlobUrl) return;
        const a = document.createElement('a');
        a.href = appState.currentBlobUrl;
        a.download = `${appState.settings.filenamePrefix}_${Date.now()}.webm`;
        a.click();
        showToast('Download initialized', 'success');
      });
      elements.btnSnapshot.addEventListener('click', captureSnapshot);
      elements.btnSaveDB.addEventListener('click', saveToLibrary);

      // Library Modal
      elements.btnLibrary.addEventListener('click', () => {
        refreshLibraryUI();
        elements.libraryModal.classList.remove('hidden');
      });
      elements.btnCloseLibrary.addEventListener('click', () => elements.libraryModal.classList.add('hidden'));

      // Keyboard Shortcuts Modal & Hotkeys
      elements.btnShortcuts.addEventListener('click', () => elements.shortcutsModal.classList.remove('hidden'));
      elements.btnCloseShortcuts.addEventListener('click', () => elements.shortcutsModal.classList.add('hidden'));

      window.addEventListener('keydown', (e) => {
        if (e.altKey) {
          if (e.key.toLowerCase() === 'r') {
            e.preventDefault();
            if (appState.isRecording) stopRecording();
            else startRecording();
          } else if (e.key.toLowerCase() === 'p') {
            e.preventDefault();
            togglePauseRecording();
          } else if (e.key.toLowerCase() === 'c') {
            e.preventDefault();
            toggleWebcam();
          } else if (e.key.toLowerCase() === 'm') {
            e.preventDefault();
            toggleMicrophone();
          }
        }
      });
    }

    // --- APPLICATION STARTUP ---
    window.onload = async () => {
      bindEvents();
      setupPipInteractions();
      startCanvasRenderLoop();
      
      try {
        await storage.init();
        refreshLibraryUI();
      } catch (e) {
        console.error('IndexedDB failed to open:', e);
      }

      showToast('OmniCapture Pro Ready', 'info');
    };
  </script>
</body>
</html>