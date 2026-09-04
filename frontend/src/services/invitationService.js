import apiClient from "./api";
const createInvitation = (params, channel) => apiClient.post(`/api/channels/${channel}/invitations`, params)
const getUsers = (params) => apiClient.get('/api/users', {params})
const getChanelInvitations = (channel) => apiClient.get(`/api/channels/${channel}/invitations`)
const getMyInvitations = (params)=> apiClient.get('api/me/invitations', {params});
const cancelInvitation = (channel,invitation) =>apiClient.put(`/api/channels/${channel}/invitations/${invitation}`, 
    {
        status : "cancelled"
    }
)
const acceptInvitation = (channel,invitation) =>apiClient.put(`/api/channels/${channel}/invitations/${invitation}`, 
    {
        status : "accepted"
    }
)
const declineInvitation = (channel,invitation) =>apiClient.put(`/api/channels/${channel}/invitations/${invitation}`, 
    {
        status : "declined"
    }
)
export default{
    createInvitation,
    getChanelInvitations,
    getUsers,
    getMyInvitations,
    cancelInvitation,
    acceptInvitation,
    declineInvitation
}