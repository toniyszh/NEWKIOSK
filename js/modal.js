function escapeHtml(text) {
  if (!text) return "";
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function modifiersEqual(a = [], b = []) {
  if (a.length !== b.length) return false;
  for (let i = 0; i < a.length; i++) {
    const ma = a[i],
      mb = b[i];
    if (!ma || !mb) return false;
    if (ma.name !== mb.name) return false;
    if ((Number(ma.price) || 0) !== (Number(mb.price) || 0)) return false;
  }
  return true;
}
let groupSelections = {};

function addToOrder(name, price, itemCode, event) {
  groupSelections = {};
  let modal = document.createElement("div");
  modal.classList.add("modal");

  let imageSrc = "";
  let categoryId = null;
  let subcategoryId = null;
  let itemId = null;
  let itemLookupCode = null;

  if (event && event.currentTarget) {
    const menuItem = event.currentTarget;
    categoryId = menuItem.getAttribute("data-category");
    subcategoryId = menuItem.getAttribute("data-subcategory-id");
    itemId = menuItem.getAttribute("data-item-id");
    itemLookupCode = menuItem.getAttribute("data-item-lookup-code");
    const img = menuItem.querySelector("img");
    if (img && img.src) imageSrc = img.src;
  }

  const styleSheet = document.createElement("style");
  styleSheet.textContent = `
    @keyframes modalFadeIn {
      from { opacity: 0; } to { opacity: 1; }
    }
    @keyframes slideDown {
      from { transform: translateY(-100%); opacity: 0; }
      to   { transform: translateY(0);     opacity: 1; }
    }
    @keyframes contentFadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0);    }
    }
    @keyframes cardRise {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0);    }
    }
    @keyframes numPop {
      0%   { transform: scale(1);    }
      40%  { transform: scale(1.35); }
      100% { transform: scale(1);    }
    }
    .modal-backdrop { animation: modalFadeIn 0.3s ease-out; }
    .modal-content-animated { animation: slideDown 0.4s ease-out; }
    .modal-content-animated > * {
      animation: contentFadeIn 0.5s ease-out forwards;
      animation-delay: 0.2s;
      opacity: 0;
    }
    .modal-content-animated > *:nth-child(2) { animation-delay: 0.3s; }
    .modal-content-animated > *:nth-child(3) { animation-delay: 0.4s; }

    .cust-section {
      width: 100%;
      max-width: 900px;
      margin-top: clamp(18px, 3vw, 28px);
    }

    .cust-group {
      margin-bottom: 28px;
    }
    .cust-group:last-child {
      margin-bottom: 0;
    }
    .cust-group-title {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: #152d07;
      opacity: 0.45;
      margin-bottom: 10px;
    }
    
    /* ── Toggle (binary: e.g. Hot / Iced) ── */
    .cust-toggle {
      display: inline-flex;
      border: 2px solid rgba(21, 45, 7, 0.28);
      border-radius: 999px;
      overflow: hidden;
      background: rgba(255, 239, 206, 0.55);
    }
    .cust-toggle-btn {
      cursor: pointer;
      padding: 10px 26px;
      font-size: 15px;
      font-weight: 700;
      color: #152d07;
      background: transparent;
      border: none;
      outline: none;
      font-family: inherit;
      transition: background 0.2s, color 0.2s;
      user-select: none;
      -webkit-tap-highlight-color: transparent;
    }
    .cust-toggle-btn.selected {
      background: #152d07;
      color: #ffefce;
    }
     
    .cust-pill-row {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(clamp(220px, 30vw, 320px), 1fr));
      gap: clamp(12px, 2vw, 18px);
    }
    .cust-pill {
      position: relative;
      cursor: pointer;
      border-radius: 12px;
      background: rgba(255, 239, 206, 0.53);
      border: 2px solid rgba(21, 45, 7, 0.36);
      padding: clamp(20px, 3vw, 28px);
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 14px;
      overflow: hidden;
      transition: transform 0.18s cubic-bezier(0.34,1.2,0.64,1), box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
      animation: cardRise 0.3s ease both;
      user-select: none;
      -webkit-tap-highlight-color: transparent;
    }
    .cust-pill::before {
      content: '';
      position: absolute;
      left: 0; top: 14px; bottom: 14px;
      width: 3px;
      border-radius: 0 3px 3px 0;
      background: #8c1b24;
      transform: scaleY(0);
      transform-origin: center;
      transition: transform 0.22s cubic-bezier(0.34,1.4,0.64,1);
    }
    .cust-pill::after {
      content: '';
      position: absolute;
      top: -30px; right: -30px;
      width: 80px; height: 80px;
      border-radius: 50%;
      background: rgba(21,45,7,0.04);
      transition: background 0.2s, transform 0.3s;
    }
    .cust-pill:hover {
      transform: translateY(-2px) scale(1.01);
      box-shadow: 0 6px 20px rgba(21,45,7,0.1);
      border-color: rgba(21,45,7,0.22);
    }
    .cust-pill.selected {
      background: #152d07;
      border-color: #152d07;
      box-shadow: 0 8px 26px rgba(21,45,7,0.26);
      transform: translateY(-2px) scale(1.01);
    }
    .cust-pill.selected::before { transform: scaleY(1); background: #ffefce; }
    .cust-pill.selected::after  { background: rgba(255,255,255,0.04); transform: scale(1.6); }
    .cust-pill.selected:hover   { box-shadow: 0 12px 32px rgba(21,45,7,0.3); }

    /* pill body text */
    .cust-pill-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 3px;
      min-width: 0;
    }
    .cust-pill-name {
      font-size: 20px;
      font-weight: 700;
      color: #152d07;
      line-height: 1.2;
      transition: color 0.2s;
    }
    .cust-pill.selected .cust-pill-name { color: #ffefce; }

    /* tick replaces dot */
    .cust-pill-dot {
      flex-shrink: 0;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 1.5px solid rgba(21, 45, 7, 0.36);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s, border-color 0.2s, transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
    }
    .cust-pill-dot svg {
      opacity: 0;
      transform: scale(0.2) rotate(-20deg);
      transition: opacity 0.18s ease, transform 0.22s cubic-bezier(0.34,1.56,0.64,1);
    }
    .cust-pill.selected .cust-pill-dot {
      background: #ffefce;
      border-color: #ffefce;
      transform: scale(1.1);
    }
    .cust-pill.selected .cust-pill-dot svg {
      opacity: 1;
      transform: scale(1) rotate(0deg);
    }
    
    /* ── Swatches (bar-chart tiles: e.g. Sugar Level) ── */
    .cust-swatch-row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .cust-swatch {
      cursor: pointer;
      width: 70px;
      height: 70px;
      border-radius: 14px;
      border: 2.5px solid rgba(21, 45, 7, 0.28);
      background: rgba(255, 239, 206, 0.55);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 4px;
      transition: transform 0.18s cubic-bezier(0.34,1.2,0.64,1),
                  box-shadow 0.2s, border-color 0.2s, background 0.2s;
      user-select: none;
      -webkit-tap-highlight-color: transparent;
    }
    .cust-swatch:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 14px rgba(21,45,7,0.10);
      border-color: rgba(21,45,7,0.5);
    }
    .cust-swatch.selected {
      background: #152d07;
      border-color: #152d07;
      box-shadow: 0 8px 20px rgba(21,45,7,0.22);
      transform: translateY(-2px);
    }
    .cust-swatch-bars {
      display: flex;
      gap: 3px;
      align-items: flex-end;
      height: 28px;
    }
    .cust-swatch-bar {
      width: 6px;
      border-radius: 3px;
      background: rgba(21,45,7,0.32);
      transition: background 0.2s;
    }
    .cust-swatch.selected .cust-swatch-bar {
      background: rgba(255,239,206,0.6);
    }
    .cust-swatch-lbl {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #152d07;
      transition: color 0.2s;
    }
    .cust-swatch.selected .cust-swatch-lbl {
      color: #ffefce;
    }

    #openCustModalBtn:hover { background: #152d07; color: #ffefce; }

    .cust-chip {
      display: inline-flex; align-items: center; gap: 6px;
      background: #152d07; color: #ffefce;
      font-size: 12px; font-weight: 700;
      padding: 5px 12px; border-radius: 999px;
      font-family: inherit;
    }
    .cust-chip-group {
      font-size: 10px; opacity: 0.55;
      text-transform: uppercase; letter-spacing: 0.08em;
    }

    .cust-float-overlay {
      position: fixed; inset: 0;
      background: rgba(0, 0, 0, 0.8);
      display: flex; align-items: center; justify-content: center;
      z-index: 99999;
      opacity: 1;
      transition: opacity 0.22s ease;
      padding: clamp(16px, 4vw, 28px);
      box-sizing: border-box;
    }
    .cust-float-overlay.hidden {
      opacity: 0;
      pointer-events: none;
    }
    .cust-float-panel {
      width: 100%;
      height: 500px;
      background: #ffefce;
      border-radius: 20px;
      padding: clamp(20px, 4vw, 32px);
      max-height: 80vh;
      overflow-y: auto;
      transform: scale(1);
      transition: transform 0.3s cubic-bezier(0.34,1.1,0.64,1), opacity 0.22s ease;
    }
    .cust-float-overlay.hidden .cust-float-panel {
      transform: scale(0.92);
      opacity: 0;
    }
    .cust-float-handle { display: none; }

    .cust-float-title {
      font-size: 20px; font-weight: 800;
      letter-spacing: 0.15em; text-transform: uppercase;
      color: #152d07; margin-bottom: 4px;
      margin-top: 25px;
    }
    .cust-float-sub {
      font-size: 14px; color: #152d07; opacity: 0.4;
      font-style: italic; margin-bottom: 10px;
    }
    .cust-float-group-label {
      font-size: 15px; font-weight: 700; letter-spacing: 0.22em;
      text-transform: uppercase; color: #152d07;
      margin: 20px 0 10px;
    }
    .cust-float-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: clamp(8px, 1.5vw, 14px);
    }
    .cust-float-done {
      width: 100%; margin-top: 60px; padding: clamp(12px, 3vw, 16px);
      background: #152d07; color: #ffefce; border: none;
      border-radius: 10px; font-size: clamp(15px, 3vw, 18px); font-weight: 700;
      cursor: pointer; letter-spacing: 0.06em; font-family: inherit;
    }

    .cust-group-error {
      color: #8c1b24;       
      position: relative;
    }

    .cust-group-error::after {
      content: " — required";
      font-size: 15px;
      font-weight: 700;
    }
    
    /* ── Badge state ── */
    .ao-header-count.has-selection { background: #8c1b24; }

    .ao-section {
      width: 100%;
      max-width: 900px;
      margin-top: 50px;
    }
    .ao-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 20px;
    }
    .ao-header-index {
      font-size: clamp(36px, 6vw, 52px);
      font-weight: 900;
      line-height: 1;
      color: #152d07;
      letter-spacing: -0.04em;
      user-select: none;
      flex-shrink: 0;
    }
    .ao-header-label { flex: 1; }
    .ao-header-label h3 {
      margin: 0;
      font-size: 25px;
      font-weight: 800;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: #152d07;
    }
    .ao-header-label p {
      margin: 2px 0 0;
      font-size: 15px;
      color: #152d07;
      opacity: 0.4;
      font-style: italic;
    }
    .ao-header-count {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: #152d07;
      color: #ffefce;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 700;
      flex-shrink: 0;
      transition: background 0.2s;
    }
    .ao-header-count.has-selection { background: #8c1b24; }
    .ao-header-count span { display: inline-block; }
    .ao-header-count span.pop {
      animation: numPop 0.28s cubic-bezier(0.34,1.56,0.64,1);
    }
    .ao-divider {
      width: 100%;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(21,45,7,0.36) 20%, rgba(21,45,7,0.12) 80%, transparent);
      margin-bottom: 20px;
    }
    .ao-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: clamp(12px, 2vw, 18px);
    }
    .cust-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: clamp(12px, 2vw, 18px);
    }
    .ao-card {
      position: relative;
      cursor: pointer;
      border-radius: 12px;
      background: rgba(255, 239, 206, 0.53);
      border: 2px solid rgba(21, 45, 7, 0.36);
      padding: clamp(20px, 3vw, 28px);
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 14px;
      overflow: hidden;
      transition: transform 0.18s cubic-bezier(0.34,1.2,0.64,1), box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
      animation: cardRise 0.3s ease both;
      user-select: none;
      -webkit-tap-highlight-color: transparent;
    }
    .ao-card::before {
      content: '';
      position: absolute;
      left: 0; top: 14px; bottom: 14px;
      width: 3px;
      border-radius: 0 3px 3px 0;
      background: #8c1b24;
      transform: scaleY(0);
      transform-origin: center;
      transition: transform 0.22s cubic-bezier(0.34,1.4,0.64,1);
    }
    .ao-card::after {
      content: '';
      position: absolute;
      top: -30px; right: -30px;
      width: 80px; height: 80px;
      border-radius: 50%;
      background: rgba(21,45,7,0.04);
      transition: background 0.2s, transform 0.3s;
    }
    .ao-card:hover {
      transform: translateY(-2px) scale(1.01);
      box-shadow: 0 6px 20px rgba(21,45,7,0.1);
      border-color: rgba(21,45,7,0.22);
    }
    .ao-card.selected {
      background: #152d07;
      border-color: #152d07;
      box-shadow: 0 8px 26px rgba(21,45,7,0.26);
      transform: translateY(-2px) scale(1.01);
    }
    .ao-card.selected::before { transform: scaleY(1); background: #ffefce; }
    .ao-card.selected::after  { background: rgba(255,255,255,0.04); transform: scale(1.6); }
    .ao-card.selected:hover   { box-shadow: 0 12px 32px rgba(21,45,7,0.3); }
    .ao-card-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 3px;
      min-width: 0;
    }
    .ao-card-name {
      font-size: 20px;
      font-weight: 700;
      color: #152d07;
      line-height: 1.2;
      white-space: normal;
      overflow: visible;
      text-overflow: unset;
      transition: color 0.2s;
    }
    .ao-card.selected .ao-card-name { color: #ffefce; }
    .ao-card-price {
      font-size: 15px;
      font-weight: 700;
      color: #8c1b24;
      transition: color 0.2s;
    }
    .ao-card-price.free {
      color: #152d07;
      opacity: 0.28;
      font-weight: 500;
      font-style: italic;
    }
    .ao-card.selected .ao-card-price      { color: rgba(255,239,206,0.6); }
    .ao-card.selected .ao-card-price.free { color: rgba(255,239,206,0.35); opacity: 1; }
    .ao-tick {
      flex-shrink: 0;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 1.5px solid rgba(21, 45, 7, 0.36);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s, border-color 0.2s, transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
    }
    .ao-tick svg {
      opacity: 0;
      transform: scale(0.2) rotate(-20deg);
      transition: opacity 0.18s ease, transform 0.22s cubic-bezier(0.34,1.56,0.64,1);
    }
    .ao-card.selected .ao-tick {
      background: #ffefce;
      border-color: #ffefce;
      transform: scale(1.1);
    }
    .ao-card.selected .ao-tick svg {
      opacity: 1;
      transform: scale(1) rotate(0deg);
    }
    .si-section {
      width: 100%;
      max-width: 900px;
      margin-top: 50px;
    }
    .si-input-wrapper { position: relative; }
    .si-input {
      width: 100%;
      box-sizing: border-box;
      padding: clamp(20px, 8vw, 70px);
      font-style: italic;
      font-size: 18px;
      font-weight: 400;
      border-radius: 12px;
      border: 2px solid rgba(21, 45, 7, 0.36);
      background: rgba(255,239,206,0.6);
      color: #152d07;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
      resize: none;
    }
    .si-input:focus {
      border-color: #8c1b24;
      box-shadow: 0 0 0 3px rgba(140,27,36,0.08);
    }
    .si-input::placeholder { color: rgba(21, 45, 7, 0.59); }
    .modifier-checkbox { display: none !important; }
  `;
  document.head.appendChild(styleSheet);

  modal.classList.add("modal-backdrop");

  modal.innerHTML = `
    <div class="modal-content modal-content-animated" style="width: 100vw; height: 100vh; display: flex; flex-direction: column; position: relative; 
      padding: clamp(10px, 3vw, 20px); background-image: url('../images/background/selected.png'); background-size: cover; background-position: center; margin: 0; border-radius: 0;">
    
      <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>
      
      <h2 style="text-align: center; margin: clamp(15px, 5vw, 40px) 0 clamp(10px, 3vw, 20px); position: relative; z-index: 2; font-size: clamp(18px, 4vw, 28px); color: #152d07">
        YOU'VE SELECTED:</h2>

<div class="mainContent" style="flex: 1; display: flex; flex-direction: column; align-items: center; 
  position: relative; z-index: 2; padding: 0 clamp(10px, 2vw, 20px); overflow-y: auto;">
        
        <div class="added-item" style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: clamp(15px, 3vw, 20px); width: 100%; max-width: 500px;">
          <img src="${imageSrc}" alt="${escapeHtml(name)}" class="modal-image" style="width: 100%; max-width: clamp(250px, 60vw, 400px); height: auto; object-fit: contain;">
          <p style="font-size: clamp(20px, 4vw, 30px); font-weight: bold; text-align: center; margin-top: clamp(10px, 2vw, 15px); color: #152d07; line-height: 1.3;">
            ${escapeHtml(name)} <br> 
            <strong>₱${Number(price).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>
          </p>
          
          <div class="quantity-selector" style="display: flex; align-items: center; justify-content: center; margin: clamp(15px, 3vw, 20px) 0; gap: clamp(10px, 2vw, 15px);">
            <button class="decrease-qty" style="width: clamp(45px, 10vw, 60px); height: clamp(45px, 10vw, 60px); font-size: clamp(22px, 5vw, 30px); 
              border-radius: 10px; background: #fae1d14f; color: #152d07; border: 2px solid #8c1b24; cursor: pointer;">-</button>
            <input type="text" value="1" class="qtyinput" id="quantity" style="width: clamp(50px, 12vw, 60px); height: clamp(45px, 10vw, 60px); 
              background: #fae1d14f; color: #152d07; text-align: center; font-size: clamp(20px, 4.5vw, 28px); border: 2px solid #8c1b24; border-radius: 5px;">
            <button class="increase-qty" style="width: clamp(45px, 10vw, 60px); height: clamp(45px, 10vw, 60px); font-size: clamp(22px, 5vw, 28px); 
              border-radius: 10px; background: #8c1b24; color: #ffefce; border: none; cursor: pointer;">+</button>
          </div>
        </div>

        <!-- 01 CUSTOMIZE -->
        <div class="cust-section ao-section" id="custSection" style="display: none;">
          <div class="ao-header">
            <div class="ao-header-index">01</div>
            <div class="ao-header-label">
              <h3>${kioskT("customization")}</h3>
              <p>${kioskT("customizationHint")}</p>
            </div>
            <div class="ao-header-count" id="custCount"><span>0</span></div>
          </div>
          <div class="ao-divider"></div>
          <div id="custSummaryChips" class="ao-grid" style="margin-bottom:14px; min-height:32px;">
            <span id="custEmptyMsg" style="font-size:13px; color:#152d07; opacity:0.35; font-style:italic;">${kioskT("customizationEmpty")}</span>
          </div>
          <button id="openCustModalBtn" style="
            width:100%; padding:12px; background:transparent;
            border:2px solid rgba(21,45,7,0.4); border-radius:10px;
            color:#152d07; font-size:14px; font-weight:700;
            cursor:pointer; letter-spacing:0.06em; font-family:inherit;
            transition: background 0.18s, color 0.18s;">
            ${kioskT("customizeOrder")}
          </button>
        </div>
        
        <!-- 02 ADD-ONS -->
        <div class="ao-section" id="modifiersSection" style="display: block;">
          <div class="ao-header">
            <div class="ao-header-index">02</div>
            <div class="ao-header-label">
              <h3>${kioskT("addOns")}</h3>
              <p>${kioskT("addOnsHint")}</p>
            </div>
            <div class="ao-header-count" id="aoCount"><span>0</span></div>
          </div>
          <div class="ao-divider"></div>
          <div id="modifiersGrid" class="ao-grid">
            <p style="text-align:center; color:#152d07; opacity:0.3; font-size:14px; grid-column:1/-1; padding: 20px 0;">${kioskT("loadingOptions")}</p>
          </div>
        </div>

        <!-- 03 SPECIAL INSTRUCTIONS -->
        <div class="si-section">
          <div class="ao-header">
            <div class="ao-header-index">03</div>
            <div class="ao-header-label">
              <h3>${kioskT("specialInstructions")}</h3>
              <p>${kioskT("requestsOrAllergies")}</p>
            </div>
          </div>
          <div class="ao-divider"></div>
          <div class="si-input-wrapper">
            <input id="specialInstructionInput" type="text" class="si-input"
              placeholder="${kioskT("instructionsExample")}"
              autocomplete="off" autocorrect="off" spellcheck="false">
          </div>
        </div>

      </div>

      <!-- Bottom Buttons -->
      <div class="bottom-actions" style="position: fixed; bottom: 0; left: 0; right: 0; display: flex; gap: clamp(10px, 3vw, 20px);
            padding: clamp(12px, 3vw, 18px) clamp(15px, 5vw, 40px); background: #ffefce; z-index: 10; box-sizing: border-box;">
        <button class="cancel-order btn secondary" style="flex: 1; font-size: clamp(16px, 3.5vw, 24px); padding: clamp(12px, 3vw, 18px); 
          border-radius: 10px; cursor: pointer; background: #8c1b24; color: #ffefce; border: none; max-width: 600px;">${kioskT("cancelUpper")}</button>
        <button class="add-to-order btn success" style="flex: 1; font-size: clamp(16px, 3.5vw, 24px); padding: clamp(12px, 3vw, 18px); 
          border-radius: 10px; cursor: pointer; background: #152d07; color: #ffefce; border: none; max-width: 600px;">${kioskT("addToOrder")}</button>
      </div>
    </div>
  `;

  fetch(`../api/foodAddOns.php?parentItemCode=${itemCode}`)
    .then((res) => res.json())
    .then((data) => {
      const grid = modal.querySelector("#modifiersGrid");
      const countEl = modal.querySelector("#aoCount");
      grid.innerHTML = "";

      const addOns = data.addOns || [];

      if (!addOns.length) {
        grid.innerHTML =
          `<p style='text-align:center;opacity:0.3;font-size:14px;grid-column:1/-1;padding:20px 0;'>${kioskT("noAddOns")}</p>`;
        return;
      }

      let selectedCount = 0;

      function updateCount(delta) {
        selectedCount += delta;
        const spanEl = countEl.querySelector("span");
        spanEl.textContent = selectedCount;
        spanEl.classList.remove("pop");
        void spanEl.offsetWidth;
        spanEl.classList.add("pop");
        countEl.classList.toggle("has-selection", selectedCount > 0);
      }
      function cleanText(text) {
        return String(text || "")
          .replace(/-->/g, "")
          .replace(/[^a-zA-Z0-9\s]/g, "")
          .replace(/\s+/g, " ")
          .trim();
      }

      addOns.forEach((mod, i) => {
        const rawName = mod.Name || mod.Description || mod.Modifier || "";
        const displayName = cleanText(rawName);
        const modPrice = mod.Price ? parseFloat(mod.Price) : 0;
        const isFree = modPrice === 0;
        const priceLabel = isFree ? "Free" : `+₱${modPrice.toFixed(2)}`;

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.className = "modifier-checkbox";
        checkbox.setAttribute("data-modifier", rawName);
        checkbox.setAttribute("data-price", modPrice);
        checkbox.setAttribute("data-item-option", mod.ItemOption || "");

        const card = document.createElement("div");
        card.className = "ao-card";
        card.style.animationDelay = `${i * 0.05}s`;
        card.innerHTML = `
        <div class="ao-card-body">
    <span class="ao-card-name">${escapeHtml(displayName)}</span>
    <span class="ao-card-price${isFree ? " free" : ""}">${priceLabel}</span>
  </div>
          <span class="ao-tick">
            <svg width="9" height="7" viewBox="0 0 9 7" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1 3.5L3.5 6L8 1" stroke="#152d07" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
        `;
        card.appendChild(checkbox);

        card.addEventListener("click", () => {
          const nowSelected = !card.classList.contains("selected");
          card.classList.toggle("selected", nowSelected);
          checkbox.checked = nowSelected;
          updateCount(nowSelected ? 1 : -1);
          if (typeof updateTotal === "function") updateTotal();
        });

        grid.appendChild(card);
      });
    })
    .catch((err) => {
      console.error("Error loading add-ons:", err);
      modal.querySelector("#modifiersGrid").innerHTML =
        `<p style='color:#8c1b24;text-align:center;font-size:14px;grid-column:1/-1;'>${kioskT("loadAddOnsFailed")}</p>`;
    });

  document.body.appendChild(modal);

  // ===== CUSTOMIZATION SECTION - ONLY FOR MAKE MY DAY ITEMS 100014 & 100015 =====
  const CUSTOMIZATION_ENABLED_CODES = ["100014", "100015"];
  const CUSTOMIZATION_CATEGORY_ID = 18;
  const custSection = modal.querySelector("#custSection");
  const custBody = modal.querySelector("#custSummaryChips");

if (["100014", "100015"].includes(itemLookupCode)) {
    custSection.style.display = "block";

    const floatOverlay = document.createElement("div");
    floatOverlay.className = "cust-float-overlay hidden";
    floatOverlay.innerHTML = `
    <div class="cust-float-panel">
      <div class="cust-float-handle"></div>
      <div class="cust-float-title">${kioskT("customizationTitle")}</div>
      <div class="cust-float-sub">${kioskT("customizationGroupHint")}</div>
      <div id="custFloatBody"></div>
      <button class="cust-float-done" id="custFloatDone">${kioskT("applySelections")}</button>
    </div>
  `;
    document.body.appendChild(floatOverlay);

    // Close on backdrop click
    floatOverlay.addEventListener("click", (e) => {
      if (e.target === floatOverlay) floatOverlay.classList.add("hidden");
    });

    // Open button
    modal.querySelector("#openCustModalBtn").addEventListener("click", () => {
      floatOverlay.classList.remove("hidden");
    });

    groupSelections = {};

    function cleanText(text) {
      return String(text || "")
        .replace(/-->/g, "")
        .replace(/[^a-zA-Z0-9\s]/g, "")
        .replace(/\s+/g, " ")
        .trim();
    }

    function updateSummary() {
      const chips = modal.querySelector("#custSummaryChips");
      const countEl = modal.querySelector("#custCount");
      chips.innerHTML = "";

      const entries = Object.entries(groupSelections).filter(
        ([, v]) => v !== null,
      );

      if (!entries.length) {
        // collapse to flex for the empty message
        chips.className = "";
        chips.style.cssText =
          "display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px; min-height:32px;";
        chips.innerHTML = `<span id="custEmptyMsg" style="font-size:13px;color:#152d07;opacity:0.35;font-style:italic;">${kioskT("customizationEmpty")}</span>`;
      } else {
        // restore the ao-grid class when there are entries
        chips.className = "ao-grid";
        chips.style.cssText = "margin-bottom:14px; min-height:32px;";

        entries.forEach(([group, { displayName, mod }]) => {
          const modPrice = mod?.Price ? parseFloat(mod.Price) : 0;
          const isFree = modPrice === 0;
          const priceLabel = isFree ? "Free" : `+₱${modPrice.toFixed(2)}`;

          const card = document.createElement("div");
          card.className = "ao-card selected";
          card.style.cursor = "default";
          card.innerHTML = `
        <div class="ao-card-body">
          <span style="font-size:10px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,239,206,0.45);margin-bottom:2px;">${escapeHtml(group)}</span>
          <span class="ao-card-name">${escapeHtml(displayName)}</span>
          <span class="ao-card-price${isFree ? " free" : ""}">${priceLabel}</span>
        </div>
        <span class="ao-tick">
          <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
            <path d="M1 3.5L3.5 6L8 1" stroke="#152d07" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
      `;
          chips.appendChild(card);
        });
      }

      const count = entries.length;
      const spanEl = countEl.querySelector("span");
      spanEl.textContent = count;
      spanEl.classList.remove("pop");
      void spanEl.offsetWidth;
      spanEl.classList.add("pop");
      countEl.classList.toggle("has-selection", count > 0);
    }

    function renderFloatGroup(title, items, container) {
      const labelEl = document.createElement("div");
      labelEl.className = "cust-float-group-label";
      labelEl.textContent = title;
      container.appendChild(labelEl);

      const grid = document.createElement("div");
      grid.className = "cust-float-grid";

      groupSelections[title] = null;

      items.forEach((mod, i) => {
        const rawName = mod.Name || mod.Description || mod.Modifier || "";
        const displayName = cleanText(rawName);
        const modPrice = mod.Price ? parseFloat(mod.Price) : 0;
        const isFree = modPrice === 0;
        const priceLabel = isFree ? "Free" : `+₱${modPrice.toFixed(2)}`;

        // Hidden input for form collection
        const input = document.createElement("input");
        input.type = "radio";
        input.name = `cust_${title.toLowerCase()}`;
        input.className = "modifier-checkbox customization-checkbox";
        input.setAttribute("data-modifier", rawName);
        input.setAttribute("data-price", modPrice);
        input.setAttribute(
          "data-item-option",
          mod.ItemOption || mod.ItemCode || "",
        );
        input.style.display = "none";

        const card = document.createElement("div");
        card.className = "ao-card";
        card.style.animationDelay = `${i * 0.05}s`;
        card.innerHTML = `
        <div class="ao-card-body">
          <span class="ao-card-name">${escapeHtml(displayName)}</span>
          <span class="ao-card-price${isFree ? " free" : ""}">${priceLabel}</span>
        </div>
        <span class="ao-tick">
          <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
            <path d="M1 3.5L3.5 6L8 1" stroke="#152d07" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
      `;
        card.appendChild(input);

        card.addEventListener("click", () => {
          const isAlreadySelected = groupSelections[title]?.card === card;

          // Clear all cards in this group
          grid
            .querySelectorAll(".ao-card")
            .forEach((c) => c.classList.remove("selected"));
          grid.querySelectorAll("input").forEach((r) => (r.checked = false));
          groupSelections[title] = null;

          if (!isAlreadySelected) {
            card.classList.add("selected");
            input.checked = true;
            groupSelections[title] = { card, input, displayName, mod };
          }
        });

        grid.appendChild(card);
      });

      container.appendChild(grid);
    }

    // Done button → apply to summary, close panel
    floatOverlay
      .querySelector("#custFloatDone")
      .addEventListener("click", () => {
        const missingGroups = Object.entries(groupSelections)
          .filter(([, v]) => v === null)
          .map(([group]) => group);

        if (missingGroups.length > 0) {
          const allLabels = floatOverlay.querySelectorAll(
            ".cust-float-group-label",
          );
          allLabels.forEach((label) => {
            if (missingGroups.includes(label.textContent)) {
              label.classList.add("cust-group-error");
            } else {
              label.classList.remove("cust-group-error");
            }
          });

          // Show error message
          let errorMsg = floatOverlay.querySelector("#custFloatError");
          if (!errorMsg) {
            errorMsg = document.createElement("div");
            errorMsg.id = "custFloatError";
            errorMsg.className = "cust-float-error-msg";
            floatOverlay
              .querySelector(".cust-float-panel")
              .insertBefore(
                errorMsg,
                floatOverlay.querySelector("#custFloatDone"),
              );
          }
          const missing = missingGroups.join(", ");
          errorMsg.style.display = "block";
          return;
        }

        floatOverlay
          .querySelectorAll(".cust-float-group-label")
          .forEach((label) => {
            label.classList.remove("cust-group-error");
          });
        const errorMsg = floatOverlay.querySelector("#custFloatError");
        if (errorMsg) errorMsg.style.display = "none";

        floatOverlay.classList.add("hidden");
        updateSummary();
      });

    // Remove float overlay when main modal closes
    const origRemove = modal.remove.bind(modal);
    modal.remove = () => {
      floatOverlay.remove();
      origRemove();
    };

    // Fetch and render
    fetch(`../api/foodAddOns.php?categoryId=${CUSTOMIZATION_CATEGORY_ID}`)
      .then((res) => res.json())
      .then((data) => {
        const floatBody = floatOverlay.querySelector("#custFloatBody");
        const addOns = data.addOns || [];

        if (!addOns.length) {
          floatBody.innerHTML =
            `<p style='opacity:0.3;font-size:14px;padding:20px 0;text-align:center;'>${kioskT("noCustomizationOptions")}</p>`;
          return;
        }

        const mainsCodes = ["100130", "100131", "100132"];
        const eggCodes = ["100133", "100134"];
        const groups = { Mains: [], Egg: [], Other: [] };

        addOns.forEach((mod) => {
          const code = String(mod.ItemOption || mod.ItemCode || "").trim();
          const nm = String(mod.Name || mod.Description || "")
            .trim()
            .toLowerCase();
          if (mainsCodes.includes(code)) groups.Mains.push(mod);
          else if (eggCodes.includes(code) || nm === "scrambled egg")
            groups.Egg.push(mod);
          else groups.Other.push(mod);
        });

        if (groups.Mains.length)
          renderFloatGroup("Mains", groups.Mains, floatBody);
        if (groups.Egg.length) renderFloatGroup("Egg", groups.Egg, floatBody);
        if (groups.Other.length)
          renderFloatGroup("Other", groups.Other, floatBody);
      })
      .catch(() => {
        floatOverlay.querySelector("#custFloatBody").innerHTML =
          `<p style='color:#8c1b24;font-size:14px;text-align:center;'>${kioskT("loadOptionsFailed")}</p>`;
      });
  }
  // ========================================================================================

  const quantityInput = modal.querySelector("#quantity");

  modal.querySelector(".decrease-qty").addEventListener("click", function () {
    let qty = parseInt(quantityInput.value);
    if (qty > 1) quantityInput.value = qty - 1;
  });

  modal.querySelector(".increase-qty").addEventListener("click", function () {
    let qty = parseInt(quantityInput.value);
    quantityInput.value = qty + 1;
  });

  modal.querySelector(".add-to-order").addEventListener("click", function () {
  if (["100014", "100015"].includes(itemLookupCode)) {
    const missingGroups = Object.entries(groupSelections)
      .filter(([, v]) => v === null)
      .map(([group]) => group);

    if (missingGroups.length > 0) {
      const custBtn = modal.querySelector("#openCustModalBtn");
      custBtn.style.borderColor = "#8c1b24";
      custBtn.style.color = "#8c1b24";
      custBtn.textContent = kioskT("completeCustomization");

      setTimeout(() => {
        custBtn.style.borderColor = "rgba(21,45,7,0.4)";
        custBtn.style.color = "#152d07";
        custBtn.textContent = kioskT("customizeOrder");
      }, 2500);

      return;
    }
  }

  // your existing code continues...
  const quantity = parseInt(quantityInput.value);
  const rawSpecialInstruction = (
    modal.querySelector("#specialInstructionInput")?.value || ""
  ).trim();


    let custText = "";
    const CUSTOMIZATION_ENABLED_CODES = ["100014", "100015"];
    if (
      CUSTOMIZATION_ENABLED_CODES.includes(itemLookupCode) &&
      typeof groupSelections !== "undefined"
    ) {
      const custParts = Object.entries(groupSelections)
        .filter(([, v]) => v !== null)
        .map(([, { displayName }]) => displayName);
      if (custParts.length) {
        custText = custParts.join(", ");
      }
    }

    const specialInstruction = [custText, rawSpecialInstruction]
      .filter(Boolean)
      .join(" | ");

    const selectedModifiers = [];
    modal.querySelectorAll(".modifier-checkbox:checked").forEach((checkbox) => {
      selectedModifiers.push({
        name: checkbox.getAttribute("data-modifier"),
        price: parseFloat(checkbox.getAttribute("data-price")) || 0,
        itemOption: checkbox.getAttribute("data-item-option") || "",
      });
    });

    const selectedModifiersTotal = selectedModifiers.reduce(
      (s, m) => s + (Number(m.price) || 0),
      0,
    );

    const currentOrder = JSON.parse(
      localStorage.getItem("currentOrder") || "[]",
    );

    const existingItemIndex = currentOrder.findIndex((item) => {
      if (item.itemCode !== itemCode) return false;
      if (!modifiersEqual(item.modifiers || [], selectedModifiers))
        return false;
      return (item.specialInstruction || "").trim() === specialInstruction;
    });

    if (existingItemIndex !== -1) {
      currentOrder[existingItemIndex].quantity += quantity;
      const existingModifiersTotal = (
        currentOrder[existingItemIndex].modifiers || []
      ).reduce((s, m) => s + (Number(m.price) || 0), 0);
      const unitPrice =
        Number(currentOrder[existingItemIndex].price) + existingModifiersTotal;
      currentOrder[existingItemIndex].totalPrice =
        unitPrice * currentOrder[existingItemIndex].quantity;
    } else {
      const unitPrice = Number(price) + selectedModifiersTotal;
      currentOrder.push({
        name: name,
        price: Number(price),
        quantity: quantity,
        itemCode: itemCode,
        totalPrice: unitPrice * quantity,
        imageSrc: imageSrc,
        modifiers: selectedModifiers,
        specialInstruction: specialInstruction || "",
      });
    }

    localStorage.setItem("currentOrder", JSON.stringify(currentOrder));
    if (typeof addToCart === "function") addToCart(currentOrder);

    const totals = calculateTotals(currentOrder);
    saveTotalsToLocalStorage(totals);

    let totalQuantity = 0;
    let totalPrice = 0;
    currentOrder.forEach((item) => {
      totalQuantity += item.quantity;
      totalPrice += item.totalPrice;
    });

    const quantityElement = document.querySelector(".view-cart-btn span");
    const priceElement = document.querySelector(".cart-price");
    const countBadge = document.getElementById("count");

    if (quantityElement) quantityElement.textContent = totalQuantity;
    if (priceElement) priceElement.textContent = `₱${totalPrice.toFixed(2)}`;
    if (countBadge) countBadge.textContent = totalQuantity;

    modal.remove();

    // Success toast
    const successModal = document.createElement("div");
    successModal.classList.add("modal", "success-modal");
    successModal.style.cssText = `
      position: fixed; inset: 0; z-index: 9999;
      display: flex; align-items: center; justify-content: center;
      background: rgba(0, 0, 0, 0.35);
    `;
    successModal.innerHTML = `
      <div class="modal-contents success-content" style="width: 60%; max-width: 500px; padding: 30px; text-align: center; background-color: #ffefce; border: 2px solid #152d07; border-radius: 15px; box-shadow: 0 8px 24px #8c1b2451;">
        <div class="success-icon" style="color: #8c1b24; font-size: 60px; margin-bottom: 20px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <h2 style="color: #152d07; margin-bottom: 10px;">${kioskT("addedToOrder")}</h2>
        <p style="font-size: 18px; color: #152d07; margin-bottom: 20px;">
          ${escapeHtml(kioskT(selectedModifiers.length ? "addedItemWithModifiers" : "addedItem", {
            quantity,
            name,
            modifiers: selectedModifiers.map((modifier) => modifier.name).join(", "),
          }))}
        </p>
      </div>
    `;
    document.body.appendChild(successModal);
    setTimeout(() => successModal.remove(), 1500);
  });

  modal.querySelector(".cancel-order").addEventListener("click", function () {
    modal.remove();
  });
}

function showOrderModal() {
  const orderSummaryModal = document.getElementById("orderSummaryModal");
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");

  if (currentOrder.length === 0) {
    showEmptyOrderModal();
    return;
  }

  function createEmptyOrderModal() {
    const modal = document.createElement("div");
    modal.className = "modal empty-order-modal";
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100vh";
    modal.style.zIndex = "20000";
    modal.innerHTML = `
    <div class="modal-content" style="background-color: #ffebcd; border: 3px solid #152d07; padding: 2rem; margin: 0 1rem; border-radius: 18px; width: 600px; height: 330px;">
      <div class="modal-inner" style="text-align: center; display: flex; flex-direction: column; gap: 18px;">
          <img src="../images/icons/warning.png" alt="Empty Cart" style=" max-width: 90px; height: auto; margin: 0 auto;">
    <h1 style="font-size: 35px; font-weight: 700; color: #721719; margin-bottom: 0;">${kioskT("cartEmpty")}</h1>
      <p style="font-size: 20px; color: #721719; font-weight: 500; margin: 0;">${kioskT("browsePrompt")}</p>
      <div style="display:flex; gap:12px; justify-content:center; margin-top:18px;">
        <button class="empty-order-ok" style="padding:10px 18px; border-radius:8px; display:none; background:#4CAF50; color:#fff; border:none; cursor:pointer;">OK</button>
        <button class="empty-order-browse" style="font-size: 20px; padding: 0.75rem 2rem; border-radius: 8px; background: #152d07; color: #ffebcd; cursor: pointer;">${kioskT("browseMenu")}</button>
      </div>
    </div>
  `;
    return modal;
  }

  function showEmptyOrderModal() {
    const existing = document.querySelector(".empty-order-modal");
    if (existing) return; // already shown
    const modal = createEmptyOrderModal();
    document.body.appendChild(modal);

    modal.querySelector(".empty-order-ok").addEventListener("click", () => {
      modal.remove();
    });

    modal.querySelector(".empty-order-browse").addEventListener("click", () => {
      modal.remove();
    });
  }

  const orderItemsContainer = document.getElementById("orderItemsContainer");
  orderItemsContainer.innerHTML = "";

  currentOrder.forEach((item, index) => {
    const orderItemDiv = document.createElement("div");
    orderItemDiv.className = "order-item";

    const modifiersHtml =
      item.modifiers && item.modifiers.length
        ? `<div class="modifiers-list" style="margin-top:8px; font-size:16px; color:#8c1b24;">${kioskT("addOns")}: ${item.modifiers
            .map(
              (m) =>
                `${m.name}${(Number(m.price) || 0) > 0 ? " (+₱" + Number(m.price).toFixed(2) + ")" : ""}`,
            )
            .join(", ")}</div>`
        : "";

    const instructionsHtml = item.specialInstruction
      ? `<div class="special-instruction" style="margin-top:8px; font-size:16px; color:#8c1b24;">${kioskT("specialInstructions")}: ${escapeHtml(item.specialInstruction)}</div>`
      : "";

    orderItemDiv.innerHTML = `
      <div class="order-item-image">
        <img src="${item.imageSrc || "../images/default-item.png"}" alt="${item.name}" class="item-image"/>
        <button class="remove-item-btn">×</button>
      </div>
      <div class="order-item-content">
        <h3 class="item-name">${item.name}</h3>
        <div class="price-per-item">₱${item.price.toFixed(2)} each</div>
        ${modifiersHtml}
        ${instructionsHtml}
      </div>
      <div class="order-item-controls">
        <div class="quantity-control">
          <button class="quantity-btn minus" data-index="${index}">
            <span class="minus-symbol">−</span>
          </button>
          <span class="quantity">${item.quantity}</span>
          <button class="quantity-btn plus" data-index="${index}">
            <span class="plus-symbol">+</span>
          </button>
        </div>
        <p class="item-total">₱${item.totalPrice.toFixed(2)}</p>
      </div>
    `;

    orderItemsContainer.appendChild(orderItemDiv);

    orderItemDiv
      .querySelector(".quantity-btn.minus")
      .addEventListener("click", () => updateItemQuantity(index, -1));
    orderItemDiv
      .querySelector(".quantity-btn.plus")
      .addEventListener("click", () => updateItemQuantity(index, 1));
    orderItemDiv
      .querySelector(".remove-item-btn")
      .addEventListener("click", () => removeOrderItem(index));
  });

  const totals = calculateTotals(currentOrder);
  saveTotalsToLocalStorage(totals);

  orderSummaryModal.style.display = "flex";
}

// ── Totals ──
function calculateTotals(order, serviceCharge = null) {
  const subtotal = order.reduce(
    (sum, it) => sum + (Number(it.totalPrice) || 0),
    0,
  );
  const orderType = String(localStorage.getItem("orderType") || "dine-in")
    .trim()
    .toLowerCase();
  const isTakeout = ["take-out", "takeout", "to go", "to-go", "take out"].includes(
    orderType,
  );

  let charge = serviceCharge !== null ? serviceCharge : 0;
  if (isTakeout) {
    charge = 0;
  } else if (charge === 0) {
    const stored = JSON.parse(localStorage.getItem("totals") || "{}");
    charge = stored.service_charge || 0;
  }

  const total = +(subtotal + charge).toFixed(2);

  return {
    subtotal: +subtotal.toFixed(2),
    service_charge: +parseFloat(charge).toFixed(2),
    vat: 0,
    discount: 0,
    less_vat: 0,
    total: total,
  };
}

function saveTotalsToLocalStorage(totals) {
  localStorage.setItem("totals", JSON.stringify(totals));
}

function updateOrderFooter() {
  const currentOrder = JSON.parse(localStorage.getItem("currentOrder") || "[]");
  let totalQuantity = 0;
  let totalPrice = 0;

  currentOrder.forEach((item) => {
    totalQuantity += item.quantity;
    totalPrice += item.totalPrice;
  });

  const quantityElement = document.querySelector(".view-cart-btn span");
  const priceElement = document.querySelector(".cart-price");
  const countBadge = document.getElementById("count");

  if (quantityElement) quantityElement.textContent = totalQuantity;
  if (priceElement) priceElement.textContent = `₱${totalPrice.toFixed(2)}`;
  if (countBadge) countBadge.textContent = totalQuantity;
}

document.addEventListener("DOMContentLoaded", function () {
  updateOrderFooter();

  const viewCartBtn = document.querySelector(".view-cart-btn");
  if (viewCartBtn && !viewCartBtn.onclick) {
    viewCartBtn.addEventListener("click", showOrderModal);
  }
});
