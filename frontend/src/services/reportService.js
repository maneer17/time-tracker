import apiClient from "./api";
const generateReport = (params) => apiClient.get('/api/reports', {params})
export default { generateReport }