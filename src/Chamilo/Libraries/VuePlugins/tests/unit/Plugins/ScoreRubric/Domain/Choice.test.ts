import Choice from "@/Plugins/ScoreRubric/Domain/Choice";

test('json', ()=>{
  let choice = new Choice() ;
  choice.feedback = 'my feedback';
  choice.selected = true;
  choice.hasFixedScore = true;
  choice.fixedScore = 10;

  expect(Choice.fromJSON(choice.toJSON())).toEqual(choice);
});
