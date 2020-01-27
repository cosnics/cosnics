import Choice from "../../../src/Domain/Choice";

test('json', ()=>{
  let choice = new Choice() ;
  choice.feedback = 'my feedback';
  choice.selected = true;
  choice.hasFixedScore = true;
  choice.fixedScore = 10;

  expect(Choice.fromJSON(choice.toJSON("dummy", "dummy"))).toEqual(choice);
});
