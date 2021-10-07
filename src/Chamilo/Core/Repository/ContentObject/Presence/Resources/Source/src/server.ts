import { Server } from 'miragejs';

const getStudents = () => ([
  { "id": 1, "firstname": "Joey", "lastname": "Jefferson" },
  { "id": 2, "firstname": "Freddy", "lastname": "Firstborn" },
  { "id": 3, "firstname": "Jane", "lastname": "Elderberry" }
]);

const getStatusDefaults = () => ([
  { "id": 1, "type": "fixed", "title": "Absent" },
  { "id": 2, "type": "fixed", "title": "Authorized absent" },
  { "id": 3, "type": "fixed", "title": "Present" },
  { "id": 4, "type": "semifixed", "title": "Online present", "aliasses": 3 }
]);

const getPresenceData = (_: any, request: any) => {
    const presences = [
        {
            "id": 1,
            "title": "Presence 2021-2022",
            "statuses": [
                { "id": 1, "type": "fixed", "code": "abs", "color": "deep-orange-500" },
                { "id": 2, "type": "fixed", "code": "aabs", "color": "amber-700" },
                { "id": 3, "type": "fixed", "code": "pres", "color": "lime-500" },
                { "id": 4, "type": "semifixed", "code": "online", "color": "green-300" },
                { "id": 5, "type": "custom", "code": "morn", "title": "Morning Present", "color": "yellow-100", "aliasses": 3 },
                { "id": 6, "type": "custom", "code": "after", "title": "Afternoon Present", "color": "amber-300", "aliasses": 3 }
            ]
        }
    ];
    const presence = presences.find(p => p.id === parseInt(request.params.id));
    if (!presence) { return null; }
    return {
        'status-defaults': getStatusDefaults(),
        presence
    }
};

export function makeServer({ environment = 'development' } = {}) {
  
    const server = new Server({
        routes() {
            this.namespace = 'api';

            this.get('/students', getStudents);
            this.get('/status-defaults', getStatusDefaults);
            this.get('/presence/:id', getPresenceData);

            this.post('/update-presence', () => {
                return {
                    data: 'ok!'
                }
            }, { timing: 500 });
        }
    });

    return server;
}
