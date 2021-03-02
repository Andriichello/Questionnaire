insert into LearningStage (name, description)
values ('Новачок', 'Novice'),
       ('Твердий початківець', 'Advanced beginner'),
       ('Досвідчений', 'Proficient'),
       ('Компетентний', 'Competent'),
       ('Експерт', 'Expert');



select id into @stageID from LearningStage where name = 'Новачок';
insert into Question (text, learningStageID)
value ('Переживаєте за успіх в роботі?', @stageID),
      ('Прагнете досягти швидко результату?', @stageID),
      ('Легко попадаєте в тупик при проблемах в роботі?', @stageID),
      ('Чи потрібен чіткий алгоритм для вирішення задач?', @stageID);

select id into @stageID from LearningStage where name = 'Твердий початківець';
insert into Question (text, learningStageID)
value ('Чи використовуєте власний досвід при вирішенні задач?', @stageID),
      ('Чи користуєтесь фіксованими правилами  для вирішення задач?', @stageID),
      ('Чи відчуваєте ви загальний контекст вирішення задачі?', @stageID);

select id into @stageID from LearningStage where name = 'Досвідчений';
insert into Question (text, learningStageID)
value ('Чи можете ви побудувати модель вирішуваної задачі?', @stageID),
      ('Чи вистачає вам ініціативи при вирішенні задач?', @stageID),
      ('Чи можете вирішувати проблеми, з якими ще не стикались?', @stageID);

select id into @stageID from LearningStage where name = 'Компетентний';
insert into Question (text, learningStageID)
value ('Чи  необхідний вам весь контекст задачі?', @stageID),
      ('Чи переглядаєте ви свої наміри до вирішення задачі?', @stageID),
      ('Чи здатні  ви  навчатись у інших?', @stageID);

select id into @stageID from LearningStage where name = 'Експерт';
insert into Question (text, learningStageID)
value ('Чи обираєте ви нові методи своєї роботи?', @stageID),
      ('Чи допомагає власна інтуїція при вирішенні задач?', @stageID),
      ('Чи застовуєте рішення задач за аналогією?', @stageID);


# Новачок
select id into @questionID from Question where text = 'Переживаєте за успіх в роботі?';
insert into Answer (text, mark, questionID)
value ('сильно', 5, @questionID),
      ('не дуже', 3, @questionID),
      ('спокійний', 2, @questionID);

select id into @questionID from Question where text = 'Прагнете досягти швидко результату?';
insert into Answer (text, mark, questionID)
value ('поступово', 2, @questionID),
      ('якомога швидше', 3, @questionID),
      ('дуже', 5, @questionID);

select id into @questionID from Question where text = 'Легко попадаєте в тупик при проблемах в роботі?';
insert into Answer (text, mark, questionID)
value ('так', 5, @questionID),
      ('в окремих випадках', 3, @questionID),
      ('не потрібен', 2, @questionID);

select id into @questionID from Question where text = 'Чи потрібен чіткий алгоритм для вирішення задач?';
insert into Answer (text, mark, questionID)
value ('неодмінно', 5, @questionID),
      ('поступово', 3, @questionID),
      ('зрідка', 2, @questionID);

# Твердий початківець
select id into @questionID from Question where text = 'Чи використовуєте власний досвід при вирішенні задач?';
insert into Answer (text, mark, questionID)
value ('зрідка', 5, @questionID),
      ('частково', 3, @questionID),
      ('ні', 2, @questionID);

select id into @questionID from Question where text = 'Чи користуєтесь фіксованими правилами  для вирішення задач?';
insert into Answer (text, mark, questionID)
value ('так', 2, @questionID),
      ('в окремих випадках', 3, @questionID),
      ('не потрібні', 5, @questionID);

select id into @questionID from Question where text = 'Чи відчуваєте ви загальний контекст вирішення задачі?';
insert into Answer (text, mark, questionID)
value ('так', 2, @questionID),
      ('частково', 3, @questionID),
      ('в окремих випадках', 5, @questionID);

# Досвідчений
select id into @questionID from Question where text = 'Чи можете ви побудувати модель вирішуваної задачі?';
insert into Answer (text, mark, questionID)
value ('так', 5, @questionID),
      ('не повністю', 3, @questionID),
      ('в окремих випадках', 2, @questionID);

select id into @questionID from Question where text = 'Чи вистачає вам ініціативи при вирішенні задач?';
insert into Answer (text, mark, questionID)
value ('так', 5, @questionID),
      ('зрідка', 3, @questionID),
      ('потрібне натхнення', 2, @questionID);

select id into @questionID from Question where text = 'Чи можете вирішувати проблеми, з якими ще не стикались?';
insert into Answer (text, mark, questionID)
value ('так', 2, @questionID),
      ('в окремих випадках', 3, @questionID),
      ('ні', 5, @questionID);

# Компетентний
select id into @questionID from Question where text = 'Чи  необхідний вам весь контекст задачі?';
insert into Answer (text, mark, questionID)
value ('так', 5, @questionID),
      ('в окремих деталях', 3, @questionID),
      ('в окремих в загальному', 2, @questionID);

select id into @questionID from Question where text = 'Чи переглядаєте ви свої наміри до вирішення задачі?';
insert into Answer (text, mark, questionID)
value ('так', 5, @questionID),
      ('зрідка', 3, @questionID),
      ('коли є потреба', 2, @questionID);

select id into @questionID from Question where text = 'Чи здатні  ви  навчатись у інших?';
insert into Answer (text, mark, questionID)
value ('так', 2, @questionID),
      ('зрідка', 3, @questionID),
      ('коли є потреба', 5, @questionID);

# Експерт
select id into @questionID from Question where text = 'Чи обираєте ви нові методи своєї роботи?';
insert into Answer (text, mark, questionID)
value ('так', 5, @questionID),
      ('вибірково', 3, @questionID),
      ('вистачає досвіду', 2, @questionID);

select id into @questionID from Question where text = 'Чи допомагає власна інтуїція при вирішенні задач?';
insert into Answer (text, mark, questionID)
value ('так', 5, @questionID),
      ('частково', 3, @questionID),
      ('при емоційному напруженні', 2, @questionID);

select id into @questionID from Question where text = 'Чи застовуєте рішення задач за аналогією?';
insert into Answer (text, mark, questionID)
value ('часто', 2, @questionID),
      ('зрідка', 3, @questionID),
      ('тільки власний варіант', 5, @questionID);










