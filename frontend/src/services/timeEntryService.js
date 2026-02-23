import apiClient from "./api";

const getTimeEntries = (params) => apiClient.get('/api/time-entries', {params})
const createTimeEntry = (params) => apiClient.post('/api/time-entries', params)
const deleteTimeEntry = (id) => apiClient.delete(`/api/time-entries/${id}`)

export default {

  getTimeEntries,
  createTimeEntry,
  deleteTimeEntry
};