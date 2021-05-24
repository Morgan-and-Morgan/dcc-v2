import config from '../config';

/**
 * get the unix timestamp for the current datetime
 */
export function unixTimestamp() {
    return Math.round((new Date()).getTime() / 1000);
}

export function isOffHours(d) {
    const date = d || new Date();
    const hourOfDay = date.getHours();
    const { offHours } = config;
    // we make the assumption here that our off hours start in the PM, and end in the AM the next day. Perhaps room
    // for improvement here, but should suffice for most use cases
    return hourOfDay >= offHours.start || hourOfDay <= offHours.end;
}

export function isChatHours(d) {
    // we cannot use isOffHours for chat because they start and end times are different
    const date = d || new Date();
    const hourOfDay = date.getHours();
    const { weekdayChatHours, weekendChatHours } = config;
    let chatHours;

    if (isWeekday()) {
      chatHours = weekdayChatHours;
    } else {
      chatHours = weekendChatHours;
    }

    return hourOfDay >= chatHours.start && hourOfDay < chatHours.end;
}

export function isWeekday(d) {
    const date = d || new Date();
    const dayOfWeek = date.getDay();

    if (dayOfWeek >= 1 && dayOfWeek <= 5) {
        return true;
    } else {
        return false;
    }
}
