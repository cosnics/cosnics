import Criterium from "@/../../../../../ScoreRubric/src/Domain/Criterium";

test('json', () => {
    let criterium = new Criterium("my criterium");
    criterium.weight = 99;
    expect(Criterium.fromJSON(criterium.toJSON())).toEqual(criterium);
});
test('toString', () => {
    let criterium = new Criterium('test');
    let id = criterium.id;

    expect(criterium.toString()).toEqual('Criterium (id: ' + id + ', title: '+criterium.title + ')');
});
