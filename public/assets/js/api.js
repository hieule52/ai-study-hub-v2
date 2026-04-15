/**
 * AI Study Hub LMS - Core API Client Wrapper
 * Handles JWT Tokens, Base URL, and standardized JSON parsing.
 */
class ApiClient {
    constructor() {
        // Base API URL (pointing to the PHP REST API we built)
        // Since the UI is hosted on the same server, we use '/api' directly.
        // Adjust if crossing origin.
        this.baseUrl = '/api';
    }

    /**
     * Get JWT token from local storage
     */
    getToken() {
        return localStorage.getItem('jwt_token');
    }

    /**
     * Save JWT session
     */
    setToken(token, user) {
        localStorage.setItem('jwt_token', token);
        if (user) {
            localStorage.setItem('auth_user', JSON.stringify(user));
        }
    }

    /**
     * Clear Session
     */
    clearSession() {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('auth_user');
    }

    /**
     * Get parsed User data
     */
    getUser() {
        const user = localStorage.getItem('auth_user');
        return user ? JSON.parse(user) : null;
    }

    /**
     * Formats headers for fetch API
     */
    _getHeaders(isFormData = false) {
        const headers = {};
        
        if (!isFormData) {
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';
        }

        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        return headers;
    }

    /**
     * Handles Fetch responses globally
     */
    async _handleResponse(response) {
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            // Unathorized -> clear token and force login
            if (response.status === 401) {
                this.clearSession();
                window.location.href = '/login.html';
            }
            throw new Error(data.message || 'Something went wrong with the server.');
        }

        return data;
    }

    // --- HTTP Methods --- //

    async get(endpoint) {
        const res = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'GET',
            headers: this._getHeaders()
        });
        return this._handleResponse(res);
    }

    async post(endpoint, body) {
        const res = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'POST',
            headers: this._getHeaders(),
            body: JSON.stringify(body)
        });
        return this._handleResponse(res);
    }

    async put(endpoint, body) {
        const res = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'PUT',
            headers: this._getHeaders(),
            body: JSON.stringify(body)
        });
        return this._handleResponse(res);
    }

    async delete(endpoint) {
        const res = await fetch(`${this.baseUrl}${endpoint}`, {
            method: 'DELETE',
            headers: this._getHeaders()
        });
        return this._handleResponse(res);
    }
}

// Global instance
window.api = new ApiClient();
