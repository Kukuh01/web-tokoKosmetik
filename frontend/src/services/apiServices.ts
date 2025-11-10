import axios from "axios";

const BASE_URL = import.meta.env.VITE_REACT_API_URL; // domain endpoint api

const apiClient = axios.create({
    baseURL: BASE_URL,
});

export const isAxiosError = axios.isAxiosError;

export default apiClient;