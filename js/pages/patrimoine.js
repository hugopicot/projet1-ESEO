(function(){
  const readJson = (id) => {
    const el = document.getElementById(id);
    if (!el) return null;
    try { return JSON.parse(el.textContent || 'null'); } catch(e) { return null; }
  };

  const initDropdowns = () => {
    const dropdowns = Array.from(document.querySelectorAll('[data-dropdown]'));

    const closeAll = (except) => {
      dropdowns.forEach(dropdown => {
        if (dropdown === except) return;
        const button = dropdown.querySelector('[data-dropdown-toggle]');
        const menuId = button ? button.getAttribute('data-dropdown-toggle') : null;
        const menu = menuId ? document.getElementById(menuId) : null;
        if (button) button.setAttribute('aria-expanded', 'false');
        if (menu) menu.classList.remove('is-open');
      });
    };

    dropdowns.forEach(dropdown => {
      const button = dropdown.querySelector('[data-dropdown-toggle]');
      if (!button) return;
      const menu = document.getElementById(button.getAttribute('data-dropdown-toggle'));
      if (!menu) return;

      button.addEventListener('click', event => {
        event.preventDefault();
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        closeAll(isExpanded ? null : dropdown);
        if (!isExpanded) {
          button.setAttribute('aria-expanded', 'true');
          menu.classList.add('is-open');
        }
      });

      button.addEventListener('keydown', event => {
        if (event.key === 'ArrowDown') {
          event.preventDefault();
          button.setAttribute('aria-expanded', 'true');
          menu.classList.add('is-open');
          const firstItem = menu.querySelector('[role="menuitem"]');
          if (firstItem) firstItem.focus();
        }
      });

      menu.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
          closeAll();
          button.focus();
        }
      });
    });

    document.addEventListener('click', event => {
      const container = event.target.closest('[data-dropdown]');
      if (!container) closeAll();
    });

    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') closeAll();
    });
  };

  const initAssetsSection = () => {
    const list = document.getElementById('assets-list');
    const donut = document.getElementById('assets-donut-ring');
    const totalValueTarget = document.getElementById('assets-total-value');
    if (!list || !donut || !totalValueTarget) return;

    const assetsData = readJson('assets-data') || [];

    const numberFormatter = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    const percentFormatter = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const formatShare = (value) => value.toLocaleString('fr-FR', {
      minimumFractionDigits: value < 1 ? 2 : 0,
      maximumFractionDigits: value < 1 ? 2 : 0,
    });
    const formatCurrency = (value) => `${numberFormatter.format(value)} €`;
    const formatSignedCurrency = (value) => {
      if (value === null || value === undefined) return '—';
      if (value === 0) return `${numberFormatter.format(0)} €`;
      const sign = value > 0 ? '+' : '−';
      return `${sign}${numberFormatter.format(Math.abs(value))} €`;
    };
    const formatSignedPercent = (value) => {
      if (value === null || value === undefined) return '—';
      if (value === 0) return `${percentFormatter.format(0)} %`;
      const sign = value > 0 ? '+' : '−';
      return `${sign}${percentFormatter.format(Math.abs(value))} %`;
    };

    if (!Array.isArray(assetsData) || assetsData.length === 0) {
      totalValueTarget.textContent = '—';
      donut.style.background = 'conic-gradient(#e5e7eb 0 100%)';
      return;
    }

    const totalValue = assetsData.reduce((sum, asset) => sum + (asset.value || 0), 0);
    totalValueTarget.textContent = formatCurrency(totalValue);

    const fragment = document.createDocumentFragment();
    assetsData.forEach(asset => {
      const row = document.createElement('li');
      row.className = 'assets-row';
      const deltaClass = asset.deltaValue > 0 ? 'is-positive' : asset.deltaValue < 0 ? 'is-negative' : 'is-neutral';
      const delUrl = `delete_manual_asset.php?category=crypto&external_id=${encodeURIComponent(asset.external_id || '')}`;
      row.innerHTML = `
        <div class="assets-row-name">
          <span class="assets-row-arrow" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24">
              <path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
            </svg>
          </span>
          <span class="assets-row-dot" style="background:${asset.color};"></span>
          <span class="assets-row-label">${asset.name}</span>
          <a href="${delUrl}" class="assets-row-delete" title="Supprimer" aria-label="Supprimer" style="margin-left:8px;color:#ef4444;text-decoration:none">✕</a>
        </div>
        <span class="assets-row-share">${formatShare(asset.share)} %</span>
        <span class="assets-row-value">${formatCurrency(asset.value)}</span>
        <span class="assets-row-delta">
          <span class="assets-delta-amount ${deltaClass}">${formatSignedCurrency(asset.deltaValue)}</span>
          <span class="assets-delta-percent ${deltaClass}">${formatSignedPercent(asset.deltaPercent)}</span>
        </span>
      `;
      fragment.appendChild(row);
    });
    list.appendChild(fragment);

    const totalShare = assetsData.reduce((sum, asset) => sum + asset.share, 0);
    let start = 0;
    const gradientParts = assetsData.map(asset => {
      const ratio = asset.share / totalShare;
      const end = start + ratio;
      const part = `${asset.color} ${start * 100}% ${end * 100}%`;
      start = end;
      return part;
    });
    donut.style.background = `conic-gradient(${gradientParts.join(', ')})`;
  };

  const initToaster = () => {
    const initialToasts = readJson('initial-toasts') || {};
    const container = document.getElementById('toast-container');
    if (!container) return;

    const showToast = (message, type = 'info', timeout = 3500) => {
      if (!message) return;
      const toast = document.createElement('div');
      toast.className = `toast ${type === 'success' ? 'toast--success' : type === 'error' ? 'toast--error' : 'toast--info'}`;
      const text = document.createElement('span');
      text.className = 'toast-text';
      text.textContent = message;
      const close = document.createElement('button');
      close.className = 'toast-close';
      close.type = 'button';
      close.setAttribute('aria-label', 'Fermer');
      close.textContent = '×';
      close.addEventListener('click', () => {
        toast.classList.remove('is-visible');
        setTimeout(() => toast.remove(), 200);
      });
      toast.appendChild(text);
      toast.appendChild(close);
      container.appendChild(toast);
      requestAnimationFrame(() => toast.classList.add('is-visible'));
      setTimeout(() => {
        toast.classList.remove('is-visible');
        setTimeout(() => toast.remove(), 200);
      }, timeout);
    };

    if (initialToasts.success) showToast(initialToasts.success, 'success');
    if (initialToasts.error) showToast(initialToasts.error, 'error');
    window.NovaToast = { show: showToast };
  };

  const initSidebarNavigation = () => {
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const currentPage = window.location.pathname.split('/').pop();
    sidebarItems.forEach(item => {
      const href = item.getAttribute('href');
      if (href && href.includes(currentPage)) {
        sidebarItems.forEach(si => si.classList.remove('is-active'));
        item.classList.add('is-active');
        item.setAttribute('aria-current', 'page');
      }
    });
  };

  const initPortfolioChart = () => {
    const chartContainer = document.getElementById('portfolio-chart');
    if (!chartContainer || !window.LightweightCharts) return;
    const { createChart } = window.LightweightCharts;

    const CHART_HEIGHT = 320;
    const chartHost = document.createElement('div');
    chartHost.style.height = `${CHART_HEIGHT}px`;
    chartContainer.appendChild(chartHost);

    const chartOptions = {
      layout: { background: { type: 'solid', color: 'white' }, textColor: 'rgba(19,23,34,0.85)' },
      grid: { vertLines: { color: '#eee' }, horzLines: { color: '#eee' } },
      rightPriceScale: { visible: true },
      timeScale: { rightOffset: 2, borderColor: '#eee' },
      height: CHART_HEIGHT,
    };
    const chart = createChart(chartHost, chartOptions);

    const palette = ['#8b5cf6','#3b82f6','#ef4444','#10b981','#f59e0b','#06b6d4','#64748b','#a78bfa','#fb7185','#22c55e','#f97316','#84cc16'];
    let assetSeriesRefs = [];

    const buttons = [];
    const intervals = ['1D', '1W', '1M', '1Y', 'ALL'];

    const styles = `
      .buttons-container { display:flex; flex-direction:row; justify-content:flex-end; align-items:center; gap:12px; flex-wrap:wrap; margin-top:16px; }
      .buttons-container button { all: initial; font-family: -apple-system, BlinkMacSystemFont, 'Trebuchet MS', Roboto, Ubuntu, sans-serif; font-size:16px; font-style:normal; font-weight:510; line-height:24px; letter-spacing:-0.32px; padding:8px 24px; color: rgba(19, 23, 34, 1); background-color: rgba(240, 243, 250, 1); border-radius: 8px; cursor: pointer; }
      .buttons-container button:hover { background-color: rgba(224, 227, 235, 1); }
      .buttons-container button:active { background-color: rgba(209, 212, 220, 1); }
      .buttons-container button.is-active { background-color: rgba(41, 98, 255, 0.15); color: rgba(19, 23, 34, 1); }
      .buttons-container button:focus-visible { outline: 2px solid rgba(41, 98, 255, 0.35); outline-offset: 2px; }
    `;
    const stylesElement = document.createElement('style');
    stylesElement.textContent = styles;
    document.head.appendChild(stylesElement);

    const buttonsContainer = document.createElement('div');
    buttonsContainer.classList.add('buttons-container');
    chartContainer.insertBefore(buttonsContainer, chartHost);

    const toPoint = (p) => ({ time: p.time, value: Number(p.value) });
    const lastVal = (a) => {
      const pts = Array.isArray(a.points) ? a.points : [];
      if (!pts.length) return 0;
      const p = pts[pts.length - 1];
      return Number(p.value || 0);
    };

    const setChartInterval = async (interval) => {
      try {
        const res = await fetch('api_portfolio_series.php?range=' + encodeURIComponent(interval));
        const json = await res.json();
        assetSeriesRefs.forEach(s => { try { chart.removeSeries(s); } catch(e){} });
        assetSeriesRefs = [];
        let assets = Array.isArray(json.assets) ? json.assets : [];
        assets.sort((a,b) => lastVal(b) - lastVal(a));
        const topAssets = assets.slice(0, 6);
        topAssets.forEach((a,i) => {
          const s = chart.addLineSeries({ color: palette[i % palette.length], lineWidth: 1 });
          s.setData((a.points || []).map(toPoint));
          assetSeriesRefs.push(s);
        });
        chart.timeScale().fitContent();
      } catch(e) { /* ignore réseau */ }

      buttons.forEach(button => {
        const isActive = button.dataset.interval === interval;
        button.classList.toggle('is-active', isActive);
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      });
    };

    intervals.forEach(interval => {
      const button = document.createElement('button');
      button.innerText = interval;
      button.type = 'button';
      button.addEventListener('click', () => setChartInterval(interval));
      button.dataset.interval = interval;
      button.setAttribute('aria-pressed', 'false');
      buttons.push(button);
      buttonsContainer.appendChild(button);
    });

    setChartInterval('1M');

    const resizeChart = () => {
      const width = chartContainer.clientWidth || chartHost.clientWidth;
      chart.resize(width, CHART_HEIGHT);
    };
    window.addEventListener('resize', resizeChart);
    resizeChart();
  };

  document.addEventListener('DOMContentLoaded', () => {
    initDropdowns();
    initAssetsSection();
    initSidebarNavigation();
    initToaster();
    initPortfolioChart();
  });
})();