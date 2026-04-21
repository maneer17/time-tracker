// commentService.js
import apiClient from './api'

const getComments = (channelId, sharedDayId, page = 1) =>
    apiClient.get(`/api/channels/${channelId}/shared-days/${sharedDayId}/comments?page=${page}`)

const createComment = (channelId, sharedDayId, data) =>
    apiClient.post(`/api/channels/${channelId}/shared-days/${sharedDayId}/comments`, data)

const updateComment = (channelId, sharedDayId, commentId, data) =>
    apiClient.put(`/api/channels/${channelId}/shared-days/${sharedDayId}/comments/${commentId}`, data)

const deleteComment = (channelId, sharedDayId, commentId) =>
    apiClient.delete(`/api/channels/${channelId}/shared-days/${sharedDayId}/comments/${commentId}`)

export default { getComments, createComment, updateComment, deleteComment }