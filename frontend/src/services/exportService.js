import apiClient from "./api";

const exportTimeEntries = (params) =>
    apiClient.get('api/exports/time-entries', {
        params,
        responseType: 'blob',
    });

const exportReport = (params) =>
    apiClient.post('api/exports/report', params,
        {
        responseType: 'blob',
    });

const exportSharedDay = (id, params) =>
    apiClient.get(`api/exports/shared-days/${id}`, {
        params,
        responseType: 'blob',
    });
const exportChannelData = (id, params) =>
    apiClient.get(`api/exports/channel-data/${id}`, {
        params,
    });

export default {
    exportTimeEntries,
    exportReport,
    exportSharedDay,
    exportChannelData,
}