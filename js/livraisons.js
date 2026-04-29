// KH LIVRAISON - Gestion des Livraisons
let currentDeliveryId = null;
let deliveries = [];

document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    checkAuth();
    
    // Load deliveries
    loadDeliveries();
    
    // Event Listeners
    document.getElementById('btnNewDelivery').addEventListener('click', openNewDeliveryModal);
    document.getElementById('btnLogout').addEventListener('click', logout);
    document.getElementById('deliveryForm').addEventListener('submit', saveDelivery);
    document.getElementById('filterStatus').addEventListener('change', filterDeliveries);
    document.getElementById('filterService').addEventListener('change', filterDeliveries);
    document.getElementById('searchInput').addEventListener('input', filterDeliveries);
});

// Check Authentication
function checkAuth() {
    // Simuler la vérification de session
    const userName = sessionStorage.getItem('userName') || 'Utilisateur';
    document.getElementById('userName').textContent = userName;
}

// Logout
function logout() {
    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        sessionStorage.clear();
        window.location.href = 'index.html';
    }
}

// Load Deliveries
async function loadDeliveries() {
    try {
        const response = await fetch('php/get_livraisons.php');
        const data = await response.json();
        
        if (data.success) {
            deliveries = data.deliveries;
            displayDeliveries(deliveries);
            updateStats(deliveries);
        } else {
            console.error('Erreur:', data.message);
            showEmptyState();
        }
    } catch (error) {
        console.error('Erreur:', error);
        showEmptyState();
    }
}

// Display Deliveries
function displayDeliveries(deliveriesToShow) {
    const tbody = document.getElementById('deliveriesTableBody');
    const emptyState = document.getElementById('emptyState');
    
    if (deliveriesToShow.length === 0) {
        tbody.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    
    tbody.innerHTML = deliveriesToShow.map(delivery => `
        <tr>
            <td><strong>#${delivery.code_suivi || delivery.id}</strong></td>
            <td>${getServiceName(delivery.id_service)}</td>
            <td>${delivery.adresse}<br><small>${delivery.ville}, ${delivery.code_postal}</small></td>
            <td>${formatDate(delivery.date_commande)}</td>
            <td><span class="status-badge ${delivery.statut}">${getStatusLabel(delivery.statut)}</span></td>
            <td><strong>${delivery.prix_total}€</strong></td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon edit" onclick="editDelivery(${delivery.id})" title="Modifier">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M11.333 2A1.886 1.886 0 0114 4.667L5 13.667l-3.667.666.667-3.666L11.333 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button class="btn-icon delete" onclick="confirmDelete(${delivery.id})" title="Supprimer">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 011.334-1.334h2.666a1.333 1.333 0 011.334 1.334V4m2 0v9.333a1.333 1.333 0 01-1.334 1.334H4.667a1.333 1.333 0 01-1.334-1.334V4h9.334z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Update Stats
function updateStats(deliveries) {
    const enCours = deliveries.filter(d => ['en_attente', 'en_preparation', 'en_livraison'].includes(d.statut)).length;
    const livrees = deliveries.filter(d => d.statut === 'livree').length;
    const total = deliveries.length;
    
    document.getElementById('statEnCours').textContent = enCours;
    document.getElementById('statLivrees').textContent = livrees;
    document.getElementById('statTotal').textContent = total;
}

// Filter Deliveries
function filterDeliveries() {
    const statusFilter = document.getElementById('filterStatus').value;
    const serviceFilter = document.getElementById('filterService').value;
    const searchQuery = document.getElementById('searchInput').value.toLowerCase();
    
    let filtered = deliveries;
    
    if (statusFilter) {
        filtered = filtered.filter(d => d.statut === statusFilter);
    }
    
    if (serviceFilter) {
        filtered = filtered.filter(d => d.id_service == serviceFilter);
    }
    
    if (searchQuery) {
        filtered = filtered.filter(d => 
            d.adresse.toLowerCase().includes(searchQuery) ||
            d.ville.toLowerCase().includes(searchQuery) ||
            (d.code_suivi && d.code_suivi.toLowerCase().includes(searchQuery))
        );
    }
    
    displayDeliveries(filtered);
}

// Open New Delivery Modal
function openNewDeliveryModal() {
    currentDeliveryId = null;
    document.getElementById('modalTitle').textContent = 'Nouvelle livraison';
    document.getElementById('deliveryForm').reset();
    document.getElementById('delivery-id').value = '';
    document.getElementById('deliveryModal').classList.add('active');
}

// Edit Delivery
function editDelivery(id) {
    const delivery = deliveries.find(d => d.id == id);
    if (!delivery) return;
    
    currentDeliveryId = id;
    document.getElementById('modalTitle').textContent = 'Modifier la livraison';
    document.getElementById('delivery-id').value = delivery.id;
    document.getElementById('delivery-service').value = delivery.id_service;
    document.getElementById('delivery-address').value = delivery.adresse;
    document.getElementById('delivery-city').value = delivery.ville;
    document.getElementById('delivery-postal').value = delivery.code_postal;
    document.getElementById('delivery-instructions').value = delivery.instructions || '';
    
    document.getElementById('deliveryModal').classList.add('active');
}

// Save Delivery
async function saveDelivery(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    
    try {
        const url = currentDeliveryId ? 'php/update_livraison.php' : 'php/add_livraison.php';
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✅ ' + data.message);
            closeDeliveryModal();
            loadDeliveries();
        } else {
            alert('❌ ' + data.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('❌ Une erreur est survenue');
    } finally {
        submitBtn.disabled = false;
    }
}

// Confirm Delete
function confirmDelete(id) {
    currentDeliveryId = id;
    document.getElementById('deleteModal').classList.add('active');
}

// Delete Delivery
document.addEventListener('DOMContentLoaded', function() {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function() {
            if (!currentDeliveryId) return;
            
            try {
                const response = await fetch('php/delete_livraison.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: currentDeliveryId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ Livraison supprimée');
                    closeDeleteModal();
                    loadDeliveries();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('❌ Une erreur est survenue');
            }
        });
    }
});

// Close Modals
function closeDeliveryModal() {
    document.getElementById('deliveryModal').classList.remove('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    currentDeliveryId = null;
}

// Helper Functions
function getServiceName(serviceId) {
    const services = {
        1: 'Livraison repas',
        2: 'Colis express',
        3: 'Solution pro'
    };
    return services[serviceId] || 'Service inconnu';
}

function getStatusLabel(status) {
    const labels = {
        'en_attente': 'En attente',
        'en_preparation': 'En préparation',
        'en_livraison': 'En livraison',
        'livree': 'Livrée',
        'annulee': 'Annulée'
    };
    return labels[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showEmptyState() {
    document.getElementById('deliveriesTableBody').innerHTML = '';
    document.getElementById('emptyState').style.display = 'block';
}
