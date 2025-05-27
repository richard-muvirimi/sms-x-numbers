import type { AxiosRequestConfig } from 'axios';
import '../bootstrap';

declare global {
    interface Window {
        axios: typeof import('axios').default;
    }
}

export async function fetchApi<T>(url: string, options: Omit<AxiosRequestConfig, 'url'> = {}): Promise<T> {
    try {
        // Set default headers for this request
        const headers = {
            Accept: 'application/json',
            ...options.headers,
        };

        const response = await window.axios.request<T>({
            url,
            ...options,
            headers,
        });
        return response.data;
    } catch (error) {
        if (window.axios.isAxiosError(error) && error.response?.data?.message) {
            throw new Error(error.response.data.message);
        }
        throw error;
    }
}
