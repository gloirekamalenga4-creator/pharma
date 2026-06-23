/**
 * Admin JavaScript - Planet Dépôts Pharmaceutique
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================================================
    // CONFIRMATION DE SUPPRESSION
    // ============================================================
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

    // ============================================================
    // CHARGEMENT D'IMAGE EN APERÇU
    // ============================================================
    const imageInput = document.getElementById('image_input');
    const imagePreview = document.getElementById('image_preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // ============================================================
    // CHART.JS - STATISTIQUES
    // ============================================================
    if (document.getElementById('salesChart')) {
        // Les données sont chargées dynamiquement depuis la page
        const ctx = document.getElementById('salesChart').getContext('2d');
        const chartData = JSON.parse(document.getElementById('chart-data').textContent);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Chiffre d\'affaires',
                    data: chartData.values,
                    borderColor: '#2c7da0',
                    backgroundColor: 'rgba(44, 125, 160, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    }

    // ============================================================
    // TABLEAUX AVEC RECHERCHE
    // ============================================================
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(function(input) {
        input.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const table = this.closest('.table-responsive').querySelector('table');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });

    console.log('✅ Admin script chargé');
});