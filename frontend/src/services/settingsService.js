import apiClient from "./api";
const getEmailPreferences = ()=>apiClient.get('/api/settings/notification-preferences')
const updateEmailSettings = (params)=>apiClient.put('/api/settings/notification-preferences', params)
export default{
    getEmailPreferences, updateEmailSettings
}