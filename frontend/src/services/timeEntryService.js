import apiClient from "./api";

const getTimeEntries = (params) => apiClient.get('/api/time-entries', {params})
const createTimeEntry = (params) => apiClient.post('/api/time-entries', params)
const updateTimeEntry = (params,id) => apiClient.put(`/api/time-entries/${id}`, params)
const deleteTimeEntry = (id) => apiClient.delete(`/api/time-entries/${id}`)

export default {

  getTimeEntries,
  createTimeEntry,
  updateTimeEntry,
  deleteTimeEntry
};
