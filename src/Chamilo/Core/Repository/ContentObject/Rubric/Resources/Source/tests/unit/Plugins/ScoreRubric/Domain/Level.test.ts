import Criterium from "@/../../../../../ScoreRubric/src/Domain/Criterium";
import Level, {Signal} from "@/../../../../../ScoreRubric/src/Domain/Level";

test('json', () => {
    let level = new Level('My Level', "description", 5, Signal.GREEN, true);
    expect(Level.fromJSON(level.toJSON())).toEqual(level);
});

test('toString', () => {
    let level = new Level('My Level', "description", 5, Signal.GREEN, true);
    let id = level.id;

    expect(level.toString()).toEqual('Level (id: ' + id + ', title: '+level.title + ')');
});
