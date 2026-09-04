// commentService.js
import apiClient from './api'

const getComments = (sharedDayId, page = 1) =>
    apiClient.get(`/api/shared-days/${sharedDayId}/comments?page=${page}`)

const createComment = (sharedDayId, data) =>
    apiClient.post(`/api/shared-days/${sharedDayId}/comments`, data)

const updateComment = (sharedDayId, commentId, data) =>
    apiClient.put(`/api/shared-days/${sharedDayId}/comments/${commentId}`, data)

const deleteComment = (sharedDayId, commentId) =>
    apiClient.delete(`/api/shared-days/${sharedDayId}/comments/${commentId}`)

export default { getComments, createComment, updateComment, deleteComment }