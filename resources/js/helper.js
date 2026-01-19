import moment from 'moment';
export function formatDate(value) {
    if (value) {
        return moment(String(value)).format('dddd, MMMM D, YYYY');
    }
}

export function roleType(value) {
    switch (value) {
        case 1:
            return 'ADMIN';
        case 2:
            return 'USER';
        case 3:
            return 'Pastor';
        case 4:
            return 'Member';
        default:
            return 'UNKNOWN';
            break;
    }
}

export function appointmentStatusName(value) {
    switch (value) {
        case 1:
            return 'SCHEDULED';
        case 2:
            return 'CONFIRMED';
        case 3:
            return 'CANCELLED';
        default:
            break;
    }
}

export function statusColor(value) {
    switch (value) {
        case 1:
            return 'primary'
        case 2:
            return 'success';
        case 3:
            return 'danger';
        default:
            break;
    }
}

export function formatAmount(amount) {
    return new Intl.NumberFormat('fil-PH', {
      style: 'decimal',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(amount);
  }

export function getBankColor(bankName) {
    const bankColors = {
        'BPI': 'danger',
        'BDO': 'primary',
        'SECURITY BANK': 'success',
        'UNION BANK': 'warning',
        'RCBC': 'info',
        'METROBANK': 'secondary',
    }

    return bankColors[bankName] || 'dark';
}

export function getLink(filePath) {
    return filePath;
}

export function formatSponsors(sponsors) {
    try {
        const data = JSON.parse(sponsors);

        if (Array.isArray(data)) {// if it's an array, join with comma + space
            // capitalize each name individually, then join with comma
            return data.map(name => capitalizeFirstLetter(name)).join(', ');
        }

        // if already string just return the data
        return data;
    } catch (error) {
        // fallback: return as-is if JSON.parse fails
        return sponsors;
    }
}

export function capitalizeFirstLetter(string) {
    if (typeof string !== 'string' || string.length === 0) return string;
    return string.charAt(0).toUpperCase() + string.slice(1).toLocaleLowerCase();
}
