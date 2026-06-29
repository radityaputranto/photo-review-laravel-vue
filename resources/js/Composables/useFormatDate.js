export function useFormatDate() {
    const formatDate = (dateString, options = {}) => {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        
        // Default options for Indonesian locale
        const defaultOptions = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };

        return date.toLocaleDateString('id-ID', { ...defaultOptions, ...options });
    };

    return {
        formatDate
    };
}
