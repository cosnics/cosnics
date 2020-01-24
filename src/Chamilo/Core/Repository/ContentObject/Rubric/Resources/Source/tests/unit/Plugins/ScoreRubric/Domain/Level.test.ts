import Level, {Signal} from "../../../../../src/Domain/Level";

test('json', () => {
    let level = new Level('My Level', "description", 5, Signal.GREEN, true);
    expect(Level.fromJSON(level.toJSON())).toEqual(level);
});

test('toString', () => {
    let level = new Level('My Level', "description", 5, Signal.GREEN, true);
    let id = level.id;

    expect(level.toString()).toEqual('Level (id: ' + id + ', title: '+level.title + ')');
});
