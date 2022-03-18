import Level from '../../../src/Domain/Level';

test('json', () => {
    let level = new Level('My Level', 'description', 5, true);
    expect(Level.fromJSON(level.toJSON())).toEqual(level);
});

test('toString', () => {
    const level = new Level('My Level', 'description', 5, true);

    expect(level.toString()).toEqual(`Level (id: ${level.id}, title: ${level.title})`);
});
