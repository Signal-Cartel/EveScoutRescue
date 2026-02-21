// EVE Scout Signal Cartel Checker - Bram Boreillo
// esrc.corp.checker.js

// Auto-runs on every page load ONLY if #tendingbutton exists
// Shows warning ONLY if we can confirm pilot is NOT in Signal Cartel.
// Inserts warning right after <p id="system_and_status">...</p>

(function () {
  'use strict';

  const SIGNAL_CARTEL_CORP = 'Signal Cartel';
  const CHECK_CACHE = Object.create(null);
  const WARNING_ID = 'last_sower_warning';

  // ----------------------------
  // Fetch helper with timeout
  // ----------------------------
  async function fetchJsonWithTimeout(url, options = {}, timeoutMs = 10000) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

    try {
      const res = await fetch(url, { ...options, signal: controller.signal });

      if (res.status === 503 || res.status === 504) {
        return { error: 'ESI_OFFLINE', message: 'ESI API is temporarily unavailable' };
      }
      if (res.status === 500 || res.status === 502) {
        return { error: 'ESI_ERROR', message: 'ESI API returned a server error' };
      }

      if (!res.ok) {
        return { error: 'NO_DATA', message: `ESI returned HTTP ${res.status}` };
      }

      const text = await res.text();
      if (!text) {
        return { error: 'NO_DATA', message: 'No response from ESI' };
      }

      try {
        return JSON.parse(text);
      } catch {
        return { error: 'PARSE_ERROR', message: 'Failed to parse ESI response' };
      }
    } catch (err) {
      if (err && err.name === 'AbortError') {
        return { error: 'TIMEOUT', message: 'ESI request timed out' };
      }
      return { error: 'NETWORK_ERROR', message: 'Network error connecting to ESI' };
    } finally {
      clearTimeout(timeoutId);
    }
  }

  // ----------------------------
  // ESI calls
  // ----------------------------
  async function searchCharacterESI(characterName) {
    const url = 'https://esi.evetech.net/latest/universe/ids/';
    const data = await fetchJsonWithTimeout(url, {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify([characterName]),
    });

    if (data && data.error) return data;

    if (data.characters && data.characters.length > 0) {
      return data.characters[0].id;
    }
    return { error: 'NOT_FOUND', message: 'Character not found in ESI' };
  }

  async function getCharacterFromESI(characterId) {
    const url = `https://esi.evetech.net/latest/characters/${characterId}/`;
    const data = await fetchJsonWithTimeout(url, {
      method: 'GET',
      headers: { Accept: 'application/json' },
    });

    if (!data || data.error) return null;
    return data;
  }

  async function getCorpFromESI(corpId) {
    const url = `https://esi.evetech.net/latest/corporations/${corpId}/`;
    const data = await fetchJsonWithTimeout(url, {
      method: 'GET',
      headers: { Accept: 'application/json' },
    });

    if (!data || data.error) return null;
    return data;
  }

  // ----------------------------
  // Page preflight: ensure full history
  // ----------------------------
  function hasSowerEntries() {
    return document.querySelectorAll('tr.history td.actionSower').length > 0;
  }

  function ensureGetAllIfNeeded() {
    const url = new URL(window.location.href);

    if (url.searchParams.has('getall')) return false;
    if (hasSowerEntries()) return false;

    if (url.searchParams.has('sys')) {
      url.searchParams.set('getall', '1');
      window.location.href = url.toString();
      return true;
    }

    return false;
  }

  // ----------------------------
  // Find latest Sower
  // ----------------------------
  function findLatestSower() {
    const rows = document.querySelectorAll('tr.history');

    for (const row of rows) {
      const cells = row.querySelectorAll('td');
      if (cells.length < 3) continue;

      if (cells[2].classList.contains('actionSower')) {
        return {
          characterName: cells[1].textContent.trim(),
          date: cells[0].textContent.trim(),
        };
      }
    }

    return null;
  }

  // ----------------------------
  // Warning helpers
  // ----------------------------
  function removeWarning() {
    const el = document.getElementById(WARNING_ID);
    if (el) el.remove();
  }

  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // Only show warning when we can CONFIRM the pilot is NOT in Signal Cartel.
  function shouldShowWarning(corpInfo) {
    if (!corpInfo) return false;
    if (corpInfo.error) return false;
    if (!corpInfo.corporationName) return false;
    return corpInfo.corporationName !== SIGNAL_CARTEL_CORP;
  }

  function showWarning(sowerInfo, corpInfo) {
    // Prevent duplicates
    removeWarning();

    const target = document.getElementById('system_and_status');
    if (!target) return;

    const icon = '⚠';

    const warningHtml = `
<p id="last_sower_warning" class="systemName" style="color: yellow; line-height: .9em; font-size: 1.6em; margin-left: 24px;">
  ${icon} Verify cache ownership in-game ${icon}<br>
  <span style="font-size:.65em; font-weight: normal;">
    Latest recorded sower: ${escapeHtml(sowerInfo.characterName)}, currently with ${escapeHtml(corpInfo.corporationName)}<br>
    It is likely Signal Cartel does not own this Cache anymore. Please check the Info panel in-game and look at brackets in space: if they don't show [1420.] and/or [SC0UT] please expire the current cache and resow at a different location!
  </span>
</p>`.trim();

    target.insertAdjacentHTML('afterend', warningHtml);
  }

  function updateWarning(sowerInfo, corpInfo) {
    if (shouldShowWarning(corpInfo)) {
      showWarning(sowerInfo, corpInfo);
    } else {
      removeWarning();
    }
  }

  // ----------------------------
  // Main runner
  // ----------------------------
  async function run() {
    if (ensureGetAllIfNeeded()) return;

    const latest = findLatestSower();
    if (!latest) {
      removeWarning();
      return;
    }

    const { characterName } = latest;

    // Per-page cache
    if (CHECK_CACHE[characterName]) {
      updateWarning(latest, CHECK_CACHE[characterName]);
      return;
    }

    const charId = await searchCharacterESI(characterName);

    if (charId && typeof charId === 'object' && charId.error) {
      CHECK_CACHE[characterName] = charId;
      updateWarning(latest, charId); // will suppress due to error
      return;
    }

    if (!charId) {
      const err = { error: 'NO_DATA', message: 'No character ID returned from ESI' };
      CHECK_CACHE[characterName] = err;
      updateWarning(latest, err); // will suppress due to error
      return;
    }

    const charData = await getCharacterFromESI(charId);
    if (!charData) {
      const err = { error: 'NO_DATA', message: 'Could not fetch character data from ESI' };
      CHECK_CACHE[characterName] = err;
      updateWarning(latest, err); // will suppress due to error
      return;
    }

    const corpData = await getCorpFromESI(charData.corporation_id);
    if (!corpData) {
      const err = { error: 'NO_DATA', message: 'Could not fetch corporation data from ESI' };
      CHECK_CACHE[characterName] = err;
      updateWarning(latest, err); // will suppress due to error
      return;
    }

    const corpInfo = {
      characterName,
      characterId: charId,
      corporationName: corpData.name,
      corporationId: charData.corporation_id,
    };

    CHECK_CACHE[characterName] = corpInfo;
    updateWarning(latest, corpInfo);
  }

  // ----------------------------
  // Auto-run gate: only if #tendingbutton exists
  // ----------------------------
  function shouldAutoRun() {
    return document.getElementById('tendingbutton') !== null;
  }

  function onReady(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  onReady(() => {
    if (!shouldAutoRun()) return;
    run();
  });

  // Optional manual rerun
  window.esrcCorpChecker = { run };
})();
