import axios from 'axios';

const api = axios.create({
    baseURL: process.env.MIX_APP_URL || 'http://localhost:8000',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    timeout: 30000, // 30 seconds
    transformResponse: [
        function (data) {
            // Log raw data for debugging
            console.log('TransformResponse - Raw data:', {
                data: data,
                type: typeof data,
                length: data ? (typeof data === 'string' ? data.length : JSON.stringify(data).length) : 0,
            });
            
            // If data is empty or blank, return null (will be handled by interceptor)
            if (!data || (typeof data === 'string' && data.trim() === '')) {
                console.warn('Empty response data in transformResponse');
                return null; // Return null to let interceptor handle it
            }
            
            // Try to parse JSON if data is string
            if (typeof data === 'string') {
                try {
                    const parsed = JSON.parse(data);
                    console.log('TransformResponse - Parsed JSON:', parsed);
                    return parsed;
                } catch (e) {
                    console.warn('Failed to parse response data as JSON:', e, 'Data:', data);
                    // Return as object with message
                    return { message: data, status_code: 200 };
                }
            }
            
            // Return data as is if already an object
            return data;
        }
    ],
});

// Request interceptor
api.interceptors.request.use(
    (config) => {
        // Ensure headers are set
        if (!config.headers) {
            config.headers = {};
        }
        
        // Set default headers
        if (!config.headers['Content-Type']) {
            config.headers['Content-Type'] = 'application/json';
        }
        if (!config.headers['Accept']) {
            config.headers['Accept'] = 'application/json';
        }
        config.headers['X-Requested-With'] = 'XMLHttpRequest';
        
        // Add authorization token if exists
        const token = localStorage.getItem('access_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        
        // Log request for debugging (remove in production)
        if (process.env.NODE_ENV === 'development') {
            console.log('API Request:', {
                method: config.method?.toUpperCase(),
                url: config.url,
                baseURL: config.baseURL,
                headers: config.headers,
                data: config.data,
            });
        }
        
        return config;
    },
    (error) => {
        console.error('Request Error:', error);
        return Promise.reject(error);
    }
);

// Response interceptor
api.interceptors.response.use(
    (response) => {
        // Log raw response for debugging
        console.log('Raw API Response:', {
            status: response.status,
            statusText: response.statusText,
            url: response.config?.url,
            data: response.data,
            dataType: typeof response.data,
            dataLength: response.data ? (typeof response.data === 'string' ? response.data.length : JSON.stringify(response.data).length) : 0,
            headers: response.headers,
            responseText: response.request?.responseText,
        });
        
        // Handle empty response body
        if (!response.data || response.data === null) {
            // Check if response text exists in request
            const responseText = response.request?.responseText || '';
            const responseXML = response.request?.responseXML;
            
            console.warn('Empty Response Data:', {
                status: response.status,
                statusText: response.statusText,
                url: response.config?.url,
                responseText: responseText,
                responseXML: responseXML,
                headers: response.headers,
            });
            
            if (responseText && responseText.trim() !== '') {
                try {
                    response.data = JSON.parse(responseText);
                    console.log('Parsed responseText as JSON:', response.data);
                } catch (e) {
                    console.warn('Failed to parse responseText as JSON:', e);
                    response.data = { 
                        message: responseText || 'Empty response from server', 
                        status_code: response.status 
                    };
                }
            } else {
                // Return a default response structure
                response.data = {
                    message: 'Empty response from server',
                    status_code: response.status,
                };
            }
        }
        
        // Check if response data is empty string or empty object
        if ((typeof response.data === 'string' && response.data.trim() === '') ||
            (typeof response.data === 'object' && response.data !== null && Object.keys(response.data).length === 0)) {
            console.warn('Empty Response Data Warning:', {
                status: response.status,
                statusText: response.statusText,
                url: response.config?.url,
                data: response.data,
            });
            
            // Return a default response structure
            response.data = {
                message: 'Empty response data from server',
                status_code: response.status,
            };
        }
        
        // Try to parse response data if it's a string
        if (typeof response.data === 'string' && response.data.trim() !== '') {
            try {
                response.data = JSON.parse(response.data);
            } catch (e) {
                console.warn('Failed to parse response data as JSON:', e);
                // Keep as string if parsing fails
            }
        }
        
        // Log parsed response for debugging
        if (process.env.NODE_ENV === 'development') {
            console.log('Parsed API Response:', {
                status: response.status,
                statusText: response.statusText,
                url: response.config?.url,
                data: response.data,
            });
        }
        
        return response;
    },
    async (error) => {
        // Log error for debugging
        console.error('API Error:', {
            message: error.message,
            status: error.response?.status,
            statusText: error.response?.statusText,
            data: error.response?.data,
            headers: error.response?.headers,
            request: error.request,
            config: error.config,
        });
        
        // Handle network errors (no response)
        if (!error.response) {
            console.error('Network Error - No response from server');
            return Promise.reject({
                ...error,
                message: 'Network Error: Unable to connect to server. Please check your internet connection.',
            });
        }
        
        // Handle empty/black response
        let responseData = error.response.data;
        
        // Check if response data is empty
        if (!responseData || 
            (typeof responseData === 'string' && responseData.trim() === '') ||
            (typeof responseData === 'object' && Object.keys(responseData).length === 0)) {
            
            // Try to get response text from request
            const responseText = error.request?.responseText || '';
            
            if (responseText && responseText.trim() !== '') {
                try {
                    responseData = JSON.parse(responseText);
                    error.response.data = responseData;
                } catch (e) {
                    console.warn('Failed to parse error responseText as JSON:', e);
                    error.response.data = {
                        status: 'failed',
                        message: responseText || error.response.statusText || 'Server returned empty response',
                        status_code: error.response.status,
                    };
                }
            } else {
                console.error('Empty Response Error:', {
                    status: error.response.status,
                    statusText: error.response.statusText,
                    url: error.config?.url,
                    headers: error.response.headers,
                    responseText: responseText,
                });
                
                // Create a proper error response
                error.response.data = {
                    status: 'failed',
                    message: error.response.statusText || 'Server returned empty response',
                    status_code: error.response.status,
                };
            }
        }
        
        const originalRequest = error.config;

        // Handle 401 Unauthorized
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            // Try to refresh token
            const refreshToken = localStorage.getItem('refresh_token');
            if (refreshToken) {
                try {
                    // Create a new axios instance for refresh token request to avoid interceptor loop
                    const refreshApi = axios.create({
                        baseURL: process.env.MIX_APP_URL || 'http://localhost:8000',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${refreshToken}`,
                        },
                    });

                    const response = await refreshApi.post('/api/v1/authentication/refresh/token', {});
                    const responseData = response.data;

                    // Handle response structure: { status, message, status_code, data: { access_token, refresh_token, ... } }
                    if (responseData.data && responseData.data.access_token) {
                        const { access_token, refresh_token: newRefreshToken } = responseData.data;
                        localStorage.setItem('access_token', access_token);
                        if (newRefreshToken) {
                            localStorage.setItem('refresh_token', newRefreshToken);
                        }

                        // Retry original request
                        originalRequest.headers.Authorization = `Bearer ${access_token}`;
                        return api(originalRequest);
                    } else if (responseData.access_token) {
                        // Handle direct response structure: { access_token, refresh_token, ... }
                        const { access_token, refresh_token: newRefreshToken } = responseData;
                        localStorage.setItem('access_token', access_token);
                        if (newRefreshToken) {
                            localStorage.setItem('refresh_token', newRefreshToken);
                        }

                        // Retry original request
                        originalRequest.headers.Authorization = `Bearer ${access_token}`;
                        return api(originalRequest);
                    } else {
                        // Invalid response format
                        throw new Error('Invalid refresh token response');
                    }
                } catch (refreshError) {
                    // Refresh failed, logout user
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('refresh_token');
                    window.location.href = '/login';
                    return Promise.reject(refreshError);
                }
            } else {
                // No refresh token, logout user
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                window.location.href = '/login';
            }
        }

        return Promise.reject(error);
    }
);

export default api;

