<!-- Scripts -->
<script>
    function openImportModal() {
        const modal = document.getElementById('importDataModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeImportModal() {
        const modal = document.getElementById('importDataModal');
        if (modal) modal.classList.add('hidden');
    }

    function openExportModal() {
        const modal = document.getElementById('exportDataModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeExportModal() {
        const modal = document.getElementById('exportDataModal');
        if (modal) modal.classList.add('hidden');
    }

    function handleImportFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('importFilePrompt').classList.add('hidden');
            document.getElementById('importFilePreview').classList.remove('hidden');
            document.getElementById('importFileNameDisplay').innerText = file.name;
            document.getElementById('importFileSizeDisplay').innerText = (file.size / 1024).toFixed(1) + ' KB';
        }
    }

    function clearImportFile(event) {
        if (event) event.stopPropagation();
        const fileInput = document.getElementById('importFileInput');
        if (fileInput) fileInput.value = '';
        document.getElementById('importFilePrompt').classList.remove('hidden');
        document.getElementById('importFilePreview').classList.add('hidden');
    }


    function toggleUserDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('userDropdownContainer');
        const menu = document.getElementById('userDropdownMenu');
        const arrow = document.getElementById('dropdownArrow');
        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    let currentBudgetAmount = 0;
    let currentInitialSisa = 0;
    let activeEditingExpense = null;

    const clusterProgramBudgets = @json($clusterProgramBudgets ?? []);
    const clusterMitraMap = @json($clusterMitraMap ?? []);

    function updateModalBudgetByCluster() {
        const clusterElem = document.getElementById('inputCluster');
        if (!clusterElem) return;
        const selectedCluster = clusterElem.value;
        const programTitle = document.getElementById('modalProgramTitle').innerText;

        if (clusterMitraMap && clusterMitraMap[selectedCluster]) {
            const mitraElem = document.getElementById('inputMitra');
            if (mitraElem) {
                mitraElem.value = clusterMitraMap[selectedCluster];
            }
        }

        let budgetItem = null;
        if (clusterProgramBudgets && clusterProgramBudgets[selectedCluster] && clusterProgramBudgets[selectedCluster][programTitle] !== undefined) {
            budgetItem = clusterProgramBudgets[selectedCluster][programTitle];
        } else if (clusterProgramBudgets && clusterProgramBudgets['all'] && clusterProgramBudgets['all'][programTitle] !== undefined) {
            budgetItem = clusterProgramBudgets['all'][programTitle];
        }

        if (budgetItem) {
            currentBudgetAmount = budgetItem.budget;
            let addBackNominal = 0;
            if (activeEditingExpense && activeEditingExpense.program_name === programTitle && activeEditingExpense.cluster === selectedCluster) {
                addBackNominal = parseFloat(activeEditingExpense.nominal_pengeluaran || 0);
            }
            currentInitialSisa = (budgetItem.sisa_sebelumnya !== undefined ? budgetItem.sisa_sebelumnya : (budgetItem.budget - (budgetItem.realisasi || 0))) + addBackNominal;
        } else {
            currentBudgetAmount = 0;
            currentInitialSisa = 0;
        }

        document.getElementById('displayBudgetProgram').value = formatRupiah(currentBudgetAmount);
        document.getElementById('inputBudgetProgram').value = currentBudgetAmount;

        const nominalInput = document.getElementById('displayNominalPengeluaran');
        if (nominalInput) {
            handleNominalInput(nominalInput);
        } else {
            document.getElementById('displaySisaBudgetProgram').value = formatRupiah(currentInitialSisa);
            document.getElementById('inputSisaBudgetProgram').value = currentInitialSisa;
        }
    }

    function syncRealtimeDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const tElem = document.getElementById('inputTanggal');
        const wElem = document.getElementById('inputWaktu');
        if (tElem) tElem.value = `${year}-${month}-${day}`;
        if (wElem) wElem.value = `${hours}:${minutes}:${seconds}`;
    }

    function openSubmitBudgetModal(title, budget) {
        activeEditingExpense = null;

        const modalTitle = document.getElementById('modalProgramTitle');
        if (modalTitle) modalTitle.innerText = title;

        const progInput = document.getElementById('inputProgramName');
        if (progInput) progInput.value = title;

        const form = document.getElementById('submitBudgetForm');
        if (form) form.action = "{{ route('budget-bk.culture-program.expense.store') }}";

        syncRealtimeDateTime();

        const clusterSelect = document.getElementById('inputCluster');
        const pageSelectedCluster = "{{ $selectedCluster ?? 'all' }}";
        if (clusterSelect) {
            if (pageSelectedCluster !== 'all' && clusterSelect.querySelector(`option[value="${pageSelectedCluster}"]`)) {
                clusterSelect.value = pageSelectedCluster;
            } else if (clusterSelect.options.length > 0) {
                clusterSelect.selectedIndex = 0;
            }
        }

        const deskripsiElem = document.getElementById('inputDeskripsi');
        if (deskripsiElem) deskripsiElem.value = '';

        const noteElem = document.getElementById('inputNote');
        if (noteElem) noteElem.value = '';

        const nominalInput = document.getElementById('displayNominalPengeluaran');
        if (nominalInput) nominalInput.value = '';

        const nomHidden = document.getElementById('inputNominalPengeluaran');
        if (nomHidden) nomHidden.value = 0;

        updateModalBudgetByCluster();

        const evidenceInput = document.getElementById('evidenceFileInput');
        if (evidenceInput) {
            evidenceInput.value = '';
            evidenceInput.setAttribute('required', 'required');
        }
        const promptElem = document.getElementById('fileUploadPrompt');
        if (promptElem) promptElem.classList.remove('hidden');
        const previewElem = document.getElementById('fileUploadPreview');
        if (previewElem) previewElem.classList.add('hidden');

        const modalElem = document.getElementById('submitBudgetModal');
        if (modalElem) modalElem.classList.remove('hidden');
    }

    function openEditBudgetModal(item) {
        activeEditingExpense = item;

        const modalTitle = document.getElementById('modalProgramTitle');
        if (modalTitle) modalTitle.innerText = item.program_name;

        const progInput = document.getElementById('inputProgramName');
        if (progInput) progInput.value = item.program_name;

        const form = document.getElementById('submitBudgetForm');
        if (form) form.action = `/budget-bk/culture-program/expense/${item.id}/update`;

        const clusterSelect = document.getElementById('inputCluster');
        if (clusterSelect && item.cluster) {
            clusterSelect.value = item.cluster;
        }

        const mitraInput = document.getElementById('inputMitra');
        if (mitraInput && item.mitra) {
            mitraInput.value = item.mitra;
        }

        const tElem = document.getElementById('inputTanggal');
        if (tElem && item.tanggal) tElem.value = item.tanggal;

        const wElem = document.getElementById('inputWaktu');
        if (wElem && item.waktu) wElem.value = item.waktu;

        const dElem = document.getElementById('inputDeskripsi');
        if (dElem && item.deskripsi) dElem.value = item.deskripsi;

        const noteElem = document.getElementById('inputNote');
        if (noteElem) noteElem.value = item.note || '';

        updateModalBudgetByCluster();

        const nominalInput = document.getElementById('displayNominalPengeluaran');
        if (nominalInput) {
            nominalInput.value = formatRupiah(item.nominal_pengeluaran || 0);
            handleNominalInput(nominalInput);
        }

        const evidenceInput = document.getElementById('evidenceFileInput');
        const promptElem = document.getElementById('fileUploadPrompt');
        const previewElem = document.getElementById('fileUploadPreview');
        if (item.evidence_path) {
            if (promptElem) promptElem.classList.add('hidden');
            if (previewElem) previewElem.classList.remove('hidden');
            const fnDisplay = document.getElementById('fileNameDisplay');
            if (fnDisplay) fnDisplay.innerText = item.evidence_original_name || 'bukti_invoice.pdf';
            const fsDisplay = document.getElementById('fileSizeDisplay');
            if (fsDisplay) fsDisplay.innerText = 'Dokumen Terunggah';
            if (evidenceInput) evidenceInput.removeAttribute('required');
        } else {
            if (promptElem) promptElem.classList.remove('hidden');
            if (previewElem) previewElem.classList.add('hidden');
            if (evidenceInput) evidenceInput.setAttribute('required', 'required');
        }

        const modalElem = document.getElementById('submitBudgetModal');
        if (modalElem) modalElem.classList.remove('hidden');
    }

    function closeSubmitBudgetModal() {
        document.getElementById('submitBudgetModal').classList.add('hidden');
    }

    function validateBudgetForm(event) {
        const fileInput = document.getElementById('evidenceFileInput');
        const previewVisible = !document.getElementById('fileUploadPreview').classList.contains('hidden');
        
        if ((!fileInput || !fileInput.files || fileInput.files.length === 0) && !previewVisible) {
            if (event) event.preventDefault();
            alert('Mohon unggah dokumen invoice (bukti) terlebih dahulu sebelum menyimpan pengajuan!');
            return false;
        }
        return true;
    }

    function handleNominalInput(inputElem) {
        let rawValue = inputElem.value.replace(/[^0-9]/g, '');
        let numericVal = parseInt(rawValue, 10) || 0;
        
        inputElem.value = formatRupiah(numericVal);
        document.getElementById('inputNominalPengeluaran').value = numericVal;

        let sisa = currentInitialSisa - numericVal;
        document.getElementById('displaySisaBudgetProgram').value = formatRupiah(sisa);
        document.getElementById('inputSisaBudgetProgram').value = sisa;
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('fileUploadPrompt').classList.add('hidden');
            document.getElementById('fileUploadPreview').classList.remove('hidden');
            document.getElementById('fileNameDisplay').innerText = file.name;
            document.getElementById('fileSizeDisplay').innerText = (file.size / 1024).toFixed(1) + ' KB';
        }
    }

    function clearSelectedFile(event) {
        event.stopPropagation();
        document.getElementById('evidenceFileInput').value = '';
        document.getElementById('fileUploadPrompt').classList.remove('hidden');
        document.getElementById('fileUploadPreview').classList.add('hidden');
    }

    // Preserve & Restore Scroll Position for Main Canvas across page reloads & form submits
    (function() {
        function initScrollPreservation() {
            const mainCanvas = document.querySelector('main');
            if (!mainCanvas) return;

            const savedScrollPos = sessionStorage.getItem('budgetBkMainScrollTop');
            if (savedScrollPos !== null) {
                mainCanvas.scrollTop = parseFloat(savedScrollPos);
                sessionStorage.removeItem('budgetBkMainScrollTop');
            }

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    if (mainCanvas) {
                        sessionStorage.setItem('budgetBkMainScrollTop', mainCanvas.scrollTop);
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initScrollPreservation);
        } else {
            initScrollPreservation();
        }
    })();
</script>
