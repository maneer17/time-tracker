import apiClient from "./api";


const createSharedDay = (params) => apiClient.post('/api/shared-days', params)
const getChannelSharedDays = (channel) => apiClient.get(`/api/channels/${channel}/shared-days`)
const getMySharedDays = ()=> apiClient.get('api/me/shared-days')
const getSharedDay = (channel, sharedDay) => apiClient.get(`/api/channels/${channel}/shared-days/${sharedDay}`)
const removeSharedDay = (channel, sharedDay) => apiClient.delete(`/api/channels/${channel}/shared-days/${sharedDay}`)


export default {

createSharedDay,
getChannelSharedDays,
getMySharedDays,
getSharedDay,
removeSharedDay
};
