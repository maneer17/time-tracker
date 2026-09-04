import apiClient from "./api";

const getChannels = (params) => apiClient.get('/api/channels', {params})
const createChannel = (params) => apiClient.post('/api/channels', params)
const getChannel = (channelId) => apiClient.get(`/api/channels/${channelId}`)
const deleteChannel = (channelId) => apiClient.delete(`/api/channels/${channelId}`)

export default {

  getChannels,
  createChannel,
  getChannel,
  deleteChannel

};
