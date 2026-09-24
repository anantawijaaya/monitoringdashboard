<!-- JAVASCRIPT & AJAX CONTROLLERS -->
<script>
    // Helper function for Catatan from MoM
    function getCatatanFromMoM(mom) {
        if (mom >= 1.0) return 'Melampaui Target';
        if (mom >= 0) return 'Mencapai Target';
        return 'Tidak Mencapai Target';
    }

    // Auto-fill Cluster based on Kabupaten selection
    function autoFillClusterFromKabupaten() {
        const rawKab = document.getElementById('inKabupaten').value || '';
        const kab = rawKab.toUpperCase().replace(/^(KAB\.|KOTA|KABUPATEN)\s+/i, '').trim();
        const clusterInput = document.getElementById('inClusterName');
        if (!kab) return;

        // 1. BALI BARAT
        if (['BULELENG', 'JEMBRANA', 'TABANAN', 'SINGARAJA', 'NEGARA'].includes(kab)) {
            clusterInput.value = 'BALI BARAT';
        }
        // 2. BALI TENGAH
        else if (['BADUNG', 'DENPASAR', 'KOTA DENPASAR'].includes(kab)) {
            clusterInput.value = 'BALI TENGAH';
        }
        // 3. BALI TIMUR
        else if (['BANGLI', 'GIANYAR', 'KARANG ASEM', 'KARANGASEM', 'KLUNGKUNG', 'NUSA PENIDA'].includes(kab)) {
            clusterInput.value = 'BALI TIMUR';
        }
        // 4. ENDE SIKKA
        else if (['ENDE', 'SIKKA', 'MAUMERE'].includes(kab)) {
            clusterInput.value = 'ENDE SIKKA';
        }
        // 5. FLORES TIMUR
        else if (['ALOR', 'FLORES TIMUR', 'LEMBATA', 'LARANTUKA', 'KALABAHI', 'LEWOLEBA'].includes(kab)) {
            clusterInput.value = 'FLORES TIMUR';
        }
        // 6. MANGGARAI
        else if (['MANGGARAI', 'MANGGARAI BARAT', 'MANGGARAI TIMUR', 'NAGEKEO', 'NGADA', 'LABUAN BAJO', 'RUTENG', 'BORONG', 'MBAY', 'BAJAWA'].includes(kab)) {
            clusterInput.value = 'MANGGARAI';
        }
        // 7. KUPANG ROTE
        else if (['KUPANG', 'KOTA KUPANG', 'ROTE NDAO', 'ROTE', 'BAA', 'OELAMASI'].includes(kab)) {
            clusterInput.value = 'KUPANG ROTE';
        }
        // 8. MALAKA TIMTIM BELU
        else if (['BELU', 'MALAKA', 'TIMOR TENGAH SELATAN', 'TTS', 'TIMOR TENGAH UTARA', 'TTU', 'ATAMBUA', 'BETUN', 'SOE', 'KEFAMENANU'].includes(kab)) {
            clusterInput.value = 'MALAKA TIMTIM BELU';
        }
        // 9. SUMBA
        else if (['SABU RAIJUA', 'SUMBA BARAT', 'SUMBA BARAT DAYA', 'SUMBA TENGAH', 'SUMBA TIMUR', 'WAIKABUBAK', 'TAMBOLAKA', 'WAIBAKUL', 'WAINGAPU', 'MENIA'].includes(kab)) {
            clusterInput.value = 'SUMBA';
        }
        // 10. LOMBOK
        else if (['MATARAM', 'KOTA MATARAM', 'LOMBOK BARAT', 'LOMBOK TENGAH', 'LOMBOK TIMUR', 'LOMBOK UTARA', 'GERUNG', 'PRAYA', 'SELONG', 'TANJUNG'].includes(kab)) {
            clusterInput.value = 'LOMBOK';
        }
        // 11. SUMBAWA
        else if (['SUMBAWA', 'SUMBAWA BARAT', 'SUMBAWA BESAR', 'TALIWANG'].includes(kab)) {
            clusterInput.value = 'SUMBAWA';
        }
        // 12. SUMBAWA TIMUR
        else if (['BIMA', 'KOTA BIMA', 'DOMPU', 'WOHA', 'RABA'].includes(kab)) {
            clusterInput.value = 'SUMBAWA TIMUR';
        }
    }

    // Auto-fill Edit modal cluster based on kabupaten
    function autoFillClusterFromKabupatenEdit() {
        const rawKab = document.getElementById('editKabupaten').value || '';
        const kab = rawKab.toUpperCase().replace(/^(KAB\.|KOTA|KABUPATEN)\s+/i, '').trim();
        const clusterInput = document.getElementById('editClusterName');
        if (!kab) return;

        if (['BULELENG', 'JEMBRANA', 'TABANAN', 'SINGARAJA', 'NEGARA'].includes(kab)) clusterInput.value = 'BALI BARAT';
        else if (['BADUNG', 'DENPASAR', 'KOTA DENPASAR'].includes(kab)) clusterInput.value = 'BALI TENGAH';
        else if (['BANGLI', 'GIANYAR', 'KARANG ASEM', 'KARANGASEM', 'KLUNGKUNG'].includes(kab)) clusterInput.value = 'BALI TIMUR';
        else if (['ENDE', 'SIKKA'].includes(kab)) clusterInput.value = 'ENDE SIKKA';
        else if (['ALOR', 'FLORES TIMUR', 'LEMBATA'].includes(kab)) clusterInput.value = 'FLORES TIMUR';
        else if (['MANGGARAI', 'MANGGARAI BARAT', 'MANGGARAI TIMUR', 'NAGEKEO', 'NGADA'].includes(kab)) clusterInput.value = 'MANGGARAI';
        else if (['KUPANG', 'KOTA KUPANG', 'ROTE NDAO'].includes(kab)) clusterInput.value = 'KUPANG ROTE';
        else if (['BELU', 'MALAKA', 'TIMOR TENGAH SELATAN', 'TTS', 'TIMOR TENGAH UTARA', 'TTU'].includes(kab)) clusterInput.value = 'MALAKA TIMTIM BELU';
        else if (['SABU RAIJUA', 'SUMBA BARAT', 'SUMBA BARAT DAYA', 'SUMBA TENGAH', 'SUMBA TIMUR'].includes(kab)) clusterInput.value = 'SUMBA';
        else if (['MATARAM', 'KOTA MATARAM', 'LOMBOK BARAT', 'LOMBOK TENGAH', 'LOMBOK TIMUR', 'LOMBOK UTARA'].includes(kab)) clusterInput.value = 'LOMBOK';
        else if (['SUMBAWA', 'SUMBAWA BARAT'].includes(kab)) clusterInput.value = 'SUMBAWA';
        else if (['BIMA', 'KOTA BIMA', 'DOMPU'].includes(kab)) clusterInput.value = 'SUMBAWA TIMUR';
    }

    // Modal Controls: Manual Add
    function openManualModal() {
        const errAlert = document.getElementById('manualErrorAlert');
        if (errAlert) errAlert.classList.add('hidden');
        document.getElementById('manualModal').classList.remove('hidden');
    }
    function closeManualModal() {
        document.getElementById('manualModal').classList.add('hidden');
    }

    // Modal Controls: Edit Data
    function openEditModal(item) {
        const errAlert = document.getElementById('editErrorAlert');
        if (errAlert) errAlert.classList.add('hidden');

        document.getElementById('editRecordId').value = item.id;
        document.getElementById('editClusterName').value = item.cluster_name;
        document.getElementById('editKabupaten').value = item.kabupaten || '';
        document.getElementById('editPeriodMonth').value = item.period_month;
        document.getElementById('editPeriodYear').value = item.period_year || 2026;

        document.getElementById('editTargetAll').value = item.target_revenue_all || item.target_revenue || '';
        document.getElementById('editMtdAll').value = item.mtd_revenue_all || item.revenue_all || '';

        document.getElementById('editTargetBb').value = item.target_broadband || '';
        document.getElementById('editMtdBb').value = item.mtd_broadband || item.revenue_broadband || '';

        document.getElementById('editTargetRedeem').value = item.target_redeem || '';
        document.getElementById('editMtdRedeem').value = item.mtd_redeem || item.revenue_redeem_pv || '';

        const editTargetRgb = document.getElementById('editTargetRgb');
        if (editTargetRgb) editTargetRgb.value = item.target_rgb || '';
        const editMtdRgb = document.getElementById('editMtdRgb');
        if (editMtdRgb) editMtdRgb.value = item.mtd_rgb || item.revenue_rgb || '';

        document.getElementById('editLastMonth').value = item.revenue_last_month || '';
        document.getElementById('editCurrentMonth').value = item.revenue_current_month || item.mtd_revenue_all || item.revenue_all || '';

        document.getElementById('editNotes').value = item.notes || item.catatan || item.status || '';

        calcEditAutoPersentase();

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Modal Controls: Import
    function openImportModal() {
        const errAlert = document.getElementById('importErrorAlert');
        if (errAlert) errAlert.classList.add('hidden');
        document.getElementById('importModal').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    // Live Percentage & MoM calculation for Manual Form
    function calcAutoPersentase() {
        const targetAll = parseFloat(document.getElementById('inTargetAll').value) || 0;
        const mtdAll = parseFloat(document.getElementById('inMtdAll').value) || 0;
        const badgeAchAll = document.getElementById('badgeAchAll');
        if (targetAll > 0) {
            const ach = ((mtdAll / targetAll) * 100).toFixed(1);
            badgeAchAll.innerText = `Pencapaian: ${ach}%`;
            badgeAchAll.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchAll.innerText = 'Pencapaian: 0%';
            badgeAchAll.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetBb = parseFloat(document.getElementById('inTargetBb').value) || 0;
        const mtdBb = parseFloat(document.getElementById('inMtdBb').value) || 0;
        const badgeAchBb = document.getElementById('badgeAchBb');
        if (targetBb > 0) {
            const ach = ((mtdBb / targetBb) * 100).toFixed(1);
            badgeAchBb.innerText = `Pencapaian: ${ach}%`;
            badgeAchBb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchBb.innerText = 'Pencapaian: 0%';
            badgeAchBb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetRedeem = parseFloat(document.getElementById('inTargetRedeem').value) || 0;
        const mtdRedeem = parseFloat(document.getElementById('inMtdRedeem').value) || 0;
        const badgeAchRedeem = document.getElementById('badgeAchRedeem');
        if (targetRedeem > 0) {
            const ach = ((mtdRedeem / targetRedeem) * 100).toFixed(1);
            badgeAchRedeem.innerText = `Pencapaian: ${ach}%`;
            badgeAchRedeem.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchRedeem.innerText = 'Pencapaian: 0%';
            badgeAchRedeem.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetRgb = parseFloat(document.getElementById('inTargetRgb')?.value) || 0;
        const mtdRgb = parseFloat(document.getElementById('inMtdRgb')?.value) || 0;
        const badgeAchRgb = document.getElementById('badgeAchRgb');
        if (badgeAchRgb) {
            if (targetRgb > 0) {
                const ach = ((mtdRgb / targetRgb) * 100).toFixed(1);
                badgeAchRgb.innerText = `Pencapaian: ${ach}%`;
                badgeAchRgb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchRgb.innerText = 'Pencapaian: 0%';
                badgeAchRgb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }
        }

        const lastMonth = parseFloat(document.getElementById('inLastMonth').value) || 0;
        const currMonth = parseFloat(document.getElementById('inCurrentMonth').value) || 0;
        const badgeMoM = document.getElementById('badgeMoMGrowth');
        const inNotes = document.getElementById('inNotes');
        if (lastMonth > 0) {
            const mom = parseFloat((((currMonth - lastMonth) / lastMonth) * 100).toFixed(1));
            badgeMoM.innerText = `MoM: ${mom >= 0 ? '+' : ''}${mom.toFixed(1)}%`;
            badgeMoM.className = mom >= 1.0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (mom >= 0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            if (inNotes && (!inNotes.value || ['Melampaui Target', 'Mencapai Target', 'Tidak Mencapai Target', 'Optimal', 'Perlu Perhatian'].includes(inNotes.value))) {
                inNotes.value = getCatatanFromMoM(mom);
            }
        } else {
            badgeMoM.innerText = 'MoM: +0.0%';
            badgeMoM.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }
    }

    // Live Percentage & MoM calculation for Edit Form
    function calcEditAutoPersentase() {
        const targetAll = parseFloat(document.getElementById('editTargetAll').value) || 0;
        const mtdAll = parseFloat(document.getElementById('editMtdAll').value) || 0;
        const badgeAchAll = document.getElementById('editBadgeAchAll');
        if (targetAll > 0) {
            const ach = ((mtdAll / targetAll) * 100).toFixed(1);
            badgeAchAll.innerText = `Pencapaian: ${ach}%`;
            badgeAchAll.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchAll.innerText = 'Pencapaian: 0%';
            badgeAchAll.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetBb = parseFloat(document.getElementById('editTargetBb').value) || 0;
        const mtdBb = parseFloat(document.getElementById('editMtdBb').value) || 0;
        const badgeAchBb = document.getElementById('editBadgeAchBb');
        if (targetBb > 0) {
            const ach = ((mtdBb / targetBb) * 100).toFixed(1);
            badgeAchBb.innerText = `Pencapaian: ${ach}%`;
            badgeAchBb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchBb.innerText = 'Pencapaian: 0%';
            badgeAchBb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetRedeem = parseFloat(document.getElementById('editTargetRedeem').value) || 0;
        const mtdRedeem = parseFloat(document.getElementById('editMtdRedeem').value) || 0;
        const badgeAchRedeem = document.getElementById('editBadgeAchRedeem');
        if (targetRedeem > 0) {
            const ach = ((mtdRedeem / targetRedeem) * 100).toFixed(1);
            badgeAchRedeem.innerText = `Pencapaian: ${ach}%`;
            badgeAchRedeem.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
        } else {
            badgeAchRedeem.innerText = 'Pencapaian: 0%';
            badgeAchRedeem.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }

        const targetRgb = parseFloat(document.getElementById('editTargetRgb')?.value) || 0;
        const mtdRgb = parseFloat(document.getElementById('editMtdRgb')?.value) || 0;
        const badgeAchRgb = document.getElementById('editBadgeAchRgb');
        if (badgeAchRgb) {
            if (targetRgb > 0) {
                const ach = ((mtdRgb / targetRgb) * 100).toFixed(1);
                badgeAchRgb.innerText = `Pencapaian: ${ach}%`;
                badgeAchRgb.className = ach >= 100 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (ach >= 90 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            } else {
                badgeAchRgb.innerText = 'Pencapaian: 0%';
                badgeAchRgb.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
            }
        }

        const lastMonth = parseFloat(document.getElementById('editLastMonth').value) || 0;
        const currMonth = parseFloat(document.getElementById('editCurrentMonth').value) || 0;
        const badgeMoM = document.getElementById('editBadgeMoMGrowth');
        const editNotes = document.getElementById('editNotes');
        if (lastMonth > 0) {
            const mom = parseFloat((((currMonth - lastMonth) / lastMonth) * 100).toFixed(1));
            badgeMoM.innerText = `MoM: ${mom >= 0 ? '+' : ''}${mom.toFixed(1)}%`;
            badgeMoM.className = mom >= 1.0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-600 text-white' : (mom >= 0 ? 'px-2.5 py-1 rounded-lg text-xs font-black bg-yellow-400 text-gray-950' : 'px-2.5 py-1 rounded-lg text-xs font-black bg-[#ED1C24] text-white');
            if (editNotes && (!editNotes.value || ['Melampaui Target', 'Mencapai Target', 'Tidak Mencapai Target', 'Optimal', 'Perlu Perhatian'].includes(editNotes.value))) {
                editNotes.value = getCatatanFromMoM(mom);
            }
        } else {
            badgeMoM.innerText = 'MoM: +0.0%';
            badgeMoM.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-gray-200 text-gray-600';
        }
    }

    // AJAX Handler: Manual Add Submit
    async function handleManualSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitManual');
        const errAlert = document.getElementById('manualErrorAlert');
        const errMsg = document.getElementById('manualErrorMsg');
        
        errAlert.classList.add('hidden');
        errMsg.innerText = '';
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Menyimpan...</span>';

        try {
            const formData = new FormData(form);
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Tersimpan!</span>';
                window.location.reload();
            } else {
                errMsg.innerText = data.message || 'Terjadi kesalahan saat menyimpan data.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (err) {
            errMsg.innerText = 'Gagal menghubungi server.';
            errAlert.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    // AJAX Handler: Edit Data Submit
    async function handleEditSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('editRecordId').value;
        const btn = document.getElementById('btnSubmitEdit');
        const errAlert = document.getElementById('editErrorAlert');
        const errMsg = document.getElementById('editErrorMsg');
        
        errAlert.classList.add('hidden');
        errMsg.innerText = '';
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Memperbarui...</span>';

        try {
            const formData = new FormData(e.target);
            const payload = Object.fromEntries(formData.entries());
            payload._method = 'PUT';

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const response = await fetch(`/revenue/${id}`, {
                method: 'POST',
                body: JSON.stringify(payload),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Berhasil!</span>';
                window.location.reload();
            } else {
                errMsg.innerText = data.message || 'Gagal memperbarui data.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (err) {
            errMsg.innerText = 'Gagal menghubungi server.';
            errAlert.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    // AJAX Handler: Import File Submit
    async function handleImportSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitImport');
        const errAlert = document.getElementById('importErrorAlert');
        const errMsg = document.getElementById('importErrorMsg');
        
        errAlert.classList.add('hidden');
        errMsg.innerText = '';
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-base"></i><span>Memproses Berkas...</span>';

        try {
            const formData = new FormData(form);
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                btn.innerHTML = '<i class="bi bi-check2-all text-base"></i><span>Selesai!</span>';
                window.location.reload();
            } else {
                errMsg.innerText = data.message || 'Gagal memproses berkas import.';
                errAlert.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (err) {
            errMsg.innerText = 'Terjadi kesalahan koneksi server.';
            errAlert.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    // AJAX Handler: Delete Cluster
    async function handleDeleteCluster(e, url, name) {
        e.preventDefault();
        if (!confirm(`Hapus data cluster "${name}"?`)) return;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ _method: 'DELETE' })
            });

            const data = await response.json().catch(() => ({}));
            if (response.ok && data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal menghapus data.');
            }
        } catch (err) {
            alert('Gagal menghubungi server.');
        }
    }

    // Dropdown Toggle
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('userDropdownContainer');
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    });

    // Close modal when clicking outside modal box (backdrop)
    ['manualModal', 'editModal', 'importModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }
    });

    function toggleKpiSbpMenu() {
        const submenu = document.getElementById('kpiSbpSubmenu');
        const arrow = document.getElementById('kpiSbpArrow');
        if (!submenu || !arrow) return;

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.classList.add('rotate-90');
            arrow.style.transform = '';
            localStorage.setItem('sidebarKpiSbpOpen', 'true');
        } else {
            submenu.classList.add('hidden');
            arrow.classList.remove('rotate-90');
            arrow.style.transform = '';
            localStorage.setItem('sidebarKpiSbpOpen', 'false');
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const isOpened = localStorage.getItem('sidebarKpiSbpOpen');
        const submenu = document.getElementById('kpiSbpSubmenu');
        const arrow = document.getElementById('kpiSbpArrow');
        if (isOpened === 'false' && submenu && arrow) {
            submenu.classList.add('hidden');
            arrow.classList.remove('rotate-90');
            arrow.style.transform = '';
        } else if (isOpened === 'true' && submenu && arrow) {
            submenu.classList.remove('hidden');
            arrow.classList.add('rotate-90');
            arrow.style.transform = '';
        }

        // Restore Sidebar collapse state
        const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isSidebarCollapsed) {
            const sidebar = document.getElementById('mainSidebar');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const headerIcon = document.getElementById('headerSidebarToggleIcon');
            document.documentElement.classList.add('sidebar-is-collapsed');
            if (sidebar) sidebar.classList.add('-ml-60');
            if (toggleIcon) toggleIcon.className = 'bi bi-chevron-right text-xs transition-transform duration-300';
            if (toggleBtn) toggleBtn.title = 'Tampilkan Sidebar';
            if (headerIcon) headerIcon.className = 'bi bi-layout-sidebar text-lg';
        }
    });

    // Alur Logika Sistem: Footer HANYA muncul saat scrolling menyentuh bagian paling bawah (Fixed Position, Bebas Crash)
    document.addEventListener('DOMContentLoaded', () => {
        const mainCanvas = document.querySelector('main');
        const dashboardFooter = document.getElementById('dashboardFooter');

        if (mainCanvas && dashboardFooter) {
            const checkFooterVisibility = () => {
                const isAtBottom = (mainCanvas.scrollTop + mainCanvas.clientHeight) >= (mainCanvas.scrollHeight - 25);
                if (isAtBottom) {
                    dashboardFooter.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
                    dashboardFooter.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                } else {
                    dashboardFooter.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                    dashboardFooter.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
                }
            };

            checkFooterVisibility();
            mainCanvas.addEventListener('scroll', checkFooterVisibility, { passive: true });
            window.addEventListener('resize', checkFooterVisibility, { passive: true });
        }
    });

    // Custom Dropdown Toggle Logic for Revenue Filters
    function submitRevFilter(type, val) {
        if (type === 'cluster') document.getElementById('revClusterInput').value = val;
        if (type === 'kabupaten') document.getElementById('revKabupatenInput').value = val;
        if (type === 'status') document.getElementById('revStatusInput').value = val;
        if (type === 'sort') document.getElementById('revSortInput').value = val;
        document.getElementById('revenueFilterForm').submit();
    }

    function toggleRevClusterMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('revClusterMenu');
        const arrow = document.getElementById('revClusterArrow');
        closeAllRevMenusExcept('revClusterMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleRevKabupatenMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('revKabupatenMenu');
        const arrow = document.getElementById('revKabupatenArrow');
        closeAllRevMenusExcept('revKabupatenMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleRevStatusMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('revStatusMenu');
        const arrow = document.getElementById('revStatusArrow');
        closeAllRevMenusExcept('revStatusMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function toggleRevSortMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('revSortMenu');
        const arrow = document.getElementById('revSortArrow');
        closeAllRevMenusExcept('revSortMenu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function closeAllRevMenusExcept(exceptId) {
        ['revClusterMenu', 'revKabupatenMenu', 'revStatusMenu', 'revSortMenu'].forEach(id => {
            if (id !== exceptId) {
                const m = document.getElementById(id);
                if (m) m.classList.add('hidden');
            }
        });
        ['revClusterArrow', 'revKabupatenArrow', 'revStatusArrow', 'revSortArrow'].forEach(id => {
            const arrId = id;
            const menuId = id.replace('Arrow', 'Menu');
            if (menuId !== exceptId) {
                const a = document.getElementById(arrId);
                if (a) a.classList.remove('rotate-180');
            }
        });
    }

    document.addEventListener('click', function(e) {
        const ids = [
            { container: 'revClusterDropdownContainer', menu: 'revClusterMenu', arrow: 'revClusterArrow' },
            { container: 'revKabupatenDropdownContainer', menu: 'revKabupatenMenu', arrow: 'revKabupatenArrow' },
            { container: 'revStatusDropdownContainer', menu: 'revStatusMenu', arrow: 'revStatusArrow' },
            { container: 'revSortDropdownContainer', menu: 'revSortMenu', arrow: 'revSortArrow' },
        ];

        ids.forEach(item => {
            const c = document.getElementById(item.container);
            const m = document.getElementById(item.menu);
            const a = document.getElementById(item.arrow);
            if (c && !c.contains(e.target) && m && !m.classList.contains('hidden')) {
                m.classList.add('hidden');
                if (a) a.classList.remove('rotate-180');
            }
        });
    });
</script>
