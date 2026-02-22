import apiClient from "./api";

const getTimeEntries = (params) => apiClient.get('/api/time-entries', {params})
const createTimeEntry = (params) => apiClient.post('/api/time-entries', {params})

export default {

  getTimeEntries,
  createTimeEntry
};