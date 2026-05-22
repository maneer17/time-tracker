import apiClient from "./api";
const getNotifications = (page=1)=>apiClient.get('/api/notifications', { params: { page } });
const getUnreadCount = ()=>apiClient.get('/api/notifications/unread-count');
const markAsRead = (id)=>apiClient.put(`api/notifications/${id}/read`);
const markAllAsRead = ()=>apiClient.put('/api/notifications/mark-all-as-read');
const deleteNotification = (id)=>apiClient.delete(`/api/notifications/${id}`);
export default {
    getNotifications,
    getUnreadCount,
    markAsRead,
    markAllAsRead,
    deleteNotification

}


