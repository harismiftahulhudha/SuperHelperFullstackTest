import { EventBus } from './app'

export function unAuthorize(err) {
    if(err.response.status === 401) {
        EventBus.$emit('unAuthorization', true);
    }
}
