import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Module management functionality
window.ModuleManager = {
    install(slug) {
        return fetch(`/admin/modules/${slug}/install`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json());
    },

    uninstall(slug) {
        return fetch(`/admin/modules/${slug}/uninstall`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json());
    },

    toggle(slug) {
        return fetch(`/admin/modules/${slug}/toggle`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json());
    },

    getInstalled() {
        return fetch('/api/modules/installed')
            .then(response => response.json());
    }
};

// Phase 1 specific functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add any Phase 1 specific JavaScript here
    console.log('🚀 Laravel Field Service Management - Phase 1 Core Framework Loaded');
    
    // Module status checking
    if (window.location.pathname === '/dashboard') {
        ModuleManager.getInstalled().then(data => {
            console.log('Installed modules:', data.modules);
        }).catch(error => {
            console.log('Error loading modules:', error);
        });
    }
});

// Utility functions for the application
window.Utils = {
    showToast(message, type = 'info') {
        // Simple toast notification - can be enhanced with a proper toast library
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-black' :
            'bg-blue-500 text-white'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    },

    formatDate(date) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    },

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};