/* eslint-disable */
// *******************************************************
// *******************************************************
//
// GENERATED FILE, DO NOT MODIFY
//
// Made by Victor Garcia ®
//
// https://github.com/victorgarciaesgi
// *******************************************************
// *******************************************************
// 💙

export type Maybe<T> = T | null;

export interface Form {
  id: string;
  created_at: string;
  updated_at: string;
  user: User;
  data: Maybe<any>;
  type: Maybe<FormType>;
  is_ppdb: boolean;
  is_verified: boolean;
  is_locked: boolean;
  attachments: Attachment[];
  comment: Maybe<string>;
  status: Maybe<FormStatus>;
}

export interface User {
  id: string;
  name: string;
  email: string;
  created_at: string;
  updated_at: string;
  province: Maybe<Province>;
  city: Maybe<City>;
  district: Maybe<District>;
  childrens: User[];
  child: Maybe<User>;
  attachments: Attachment[];
  articles: Article[];
  frontarticles: Article[];
  profilepicture: Maybe<Attachment>;
  followers: User[];
  requestfollowers: User[];
  followings: User[];
  rooms: Room[];
  school: Maybe<School>;
  attendances: Attendance[];
  roles: Maybe<Roles>;
  mainschool: Maybe<School>;
  balance: number;
  phone: string;
  dob: Maybe<string>;
  address: Maybe<string>;
  gender: Maybe<number>;
  /** Admin*/
  is_admin: boolean;
  /** Student*/
  myclassrooms: Classroom[];
  guardian: Maybe<User>;
  consultations: Consultation[];
  nisn: Maybe<string>;
  classtype: Maybe<Classtype>;
  parent: Maybe<User>;
  studentppdbs: StudentPpdb[];
  /** Teacher*/
  is_bimbel: boolean;
  adminschools: School[];
  homeroomschools: School[];
  headmasterschools: School[];
  academic_degree: Maybe<string>;
  specialty: Maybe<string>;
  hidden_attribute: Maybe<string[]>;
  absents: Absent[];
  schools: School[];
  subjects: Subject[];
  questions: Question[];
  exams: Exam[];
  classrooms: Classroom[];
  assigments: Assigment[];
  packagequestions: Packagequestion[];
  meetings: Meeting[];
  examresults: Examresult[];
  studentassigments: StudentAssigment[];
  studentabsents: Absent[];
  studentanswers: StudentAnswer[];
  studentconsultations: Consultation[];
  forms: Form[];
  reports: Report[];
  access: Maybe<string[]>;
  main_school: Maybe<School>;
  accesses: Access[];
  rawaccesses: Access[];
  transactions: Transaction[];
  myreports: Report[];
  quizzes: Quiz[];
  agendas: Agenda[];
  identity: Maybe<Identity[]>;
  major: Maybe<Major>;
  extracurriculars: Extracurricular[];
}

export interface Province {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  cities: City[];
}

export interface City {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  province: Maybe<Province>;
  type: Maybe<string>;
  schools: School[];
  districts: District[];
}

export interface School {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  city: Maybe<City>;
  province: Maybe<Province>;
  district: Maybe<District>;
  schooltype: Maybe<Schooltype>;
  address: Maybe<string>;
  description: Maybe<string>;
  latitude: Maybe<string>;
  longtitude: Maybe<string>;
  npsn: Maybe<string>;
  cover: Maybe<Attachment>;
  logo: Maybe<Attachment>;
  homeroomteachers: User[];
  headmasters: User[];
  admins: User[];
  counselors: User[];
  teachers: User[];
  students: User[];
  subjects: Subject[];
  classrooms: Classroom[];
  ppdbform: Maybe<FormTemplate>;
  extracurriculars: Extracurricular[];
  majors: Major[];
  waves: Wave[];
  openWaves: Wave[];
  announcements: Maybe<ArticleConnection>;
  attendances: Maybe<AttendanceConnection>;
  studentppdbs: Maybe<StudentPpdbConnection>;
}

export interface District {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  city: Maybe<City>;
}

export interface Schooltype {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  level: number;
  classtypes: Classtype[];
  schools: School[];
}

export interface Classtype {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  level: number;
  schooltype: Schooltype;
}

export interface Attachment {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  path: string;
  mime: string;
  is_processed: boolean;
  original_size: Maybe<string>;
  compressed_size: Maybe<string>;
  role: Maybe<string>;
  attachable: Maybe<Attachable>;
}

export type Attachable = User;
export interface Subject {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  schools: School[];
  meetings: Meeting[];
  abbreviation: Maybe<string>;
  description: Maybe<string>;
  type: Maybe<SubjectType>;
}

export interface Meeting {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  start_at: string;
  finish_at: Maybe<string>;
  content: Maybe<MeetingContent>;
  data: Maybe<MeetingData>;
  description: Maybe<string>;
  classroom: Classroom;
  article: Maybe<Article>;
  subject: Subject;
  user: User;
  rooms: Room[];
  attachments: Attachment[];
  attendances: Maybe<Agenda>;
}

export interface MeetingContent {
  type: Maybe<MeetingContentType>;
  content: Maybe<string>;
}

export enum MeetingContentType {
  Article = 'Article',
  Draw = 'DRAW',
}
export interface MeetingData {
  url: Maybe<string>;
  type: Maybe<MeetingMedia>;
  content: Maybe<string>;
  attachment: Maybe<Attachment>;
}

export enum MeetingMedia {
  Youtube = 'YOUTUBE',
  Image = 'IMAGE',
  Audio = 'AUDIO',
  Video = 'VIDEO',
  Document = 'DOCUMENT',
  Article_now = 'ARTICLE_NOW',
}
export interface Classroom {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  students: User[];
  user: User;
  subject: Subject;
  exams: Exam[];
  classtype: Classtype;
  meetings: Meeting[];
  name_formatted: Maybe<string>;
  meeting: Meeting[];
  assigments: Assigment[];
  absents: Absent[];
}

export interface Exam {
  id: string;
  name: string;
  time_limit: number;
  allow_show_result: boolean;
  shuffle: boolean;
  education_year_start: number;
  is_odd_semester: boolean;
  education_year_end: number;
  hint: Maybe<string>;
  description: Maybe<string>;
  created_at: string;
  updated_at: string;
  examsessions: Examsession[];
  examresults: Examresult[];
  questions: Question[];
  subject: Subject;
  classroom: Classroom;
  supervisors: User[];
  examtype: Examtype;
  user: User;
  agenda: Maybe<Agenda>;
}

export interface Examsession {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  exam: Maybe<Exam>;
  open_at: Maybe<string>;
  close_at: Maybe<string>;
  token: Maybe<string>;
}

export interface Examresult {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  exam: Exam;
  user: User;
  examsession: Examsession;
  studentanswers: StudentAnswer[];
  grade: number;
}

export interface StudentAnswer {
  id: string;
  created_at: string;
  updated_at: string;
  answer_id: Maybe<string>;
  answer: Maybe<Answer>;
  question: Question;
  examsession: Examsession;
  user: User;
  exam: Exam;
  examresult: Examresult;
  content: Maybe<string>;
  is_correct: boolean;
  is_processed: boolean;
  grade: number;
}

export interface Answer {
  id: string;
  created_at: string;
  updated_at: string;
  is_correct: boolean;
  content: Maybe<string>;
  question: Maybe<Question>;
  attachment: Maybe<Attachment>;
}

export interface Question {
  id: string;
  created_at: string;
  updated_at: string;
  user: Maybe<User>;
  subject: Subject;
  classtype: Maybe<Classtype>;
  editable: boolean;
  answers: Answer[];
  correctanswer: Maybe<Answer>;
  attachments: Attachment[];
  type: QuestionType;
  content: string;
  visibility: Maybe<Visibility>;
}

export enum QuestionType {
  Multi_choice = 'MULTI_CHOICE',
  Essay = 'ESSAY',
  Filler = 'FILLER',
  Survey = 'SURVEY',
  Slide = 'SLIDE',
  Many_answers = 'MANY_ANSWERS',
}
export enum Visibility {
  Publik = 'PUBLIK',
  Privat = 'PRIVAT',
  Selectpeople = 'SELECTPEOPLE',
}

export interface Examtype {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  exams: Exam[];
}

export interface Agenda {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  uuid: string;
  user: Maybe<User>;
  school: Maybe<School>;
  description: Maybe<string>;
  attendances: Attendance[];
  finish_at: Maybe<string>;
  agendaable_type: Maybe<string>;
}

export interface Attendance {
  id: string;
  created_at: string;
  updated_at: string;
  user: Maybe<User>;
  uuid: string;
  classroom: Maybe<Classroom>;
  school: Maybe<School>;
  subject: Maybe<Subject>;
  agenda: Agenda;
  attended: boolean;
  reason: Maybe<string>;
}

export interface Assigment {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  user: User;
  content: string;
  close_at: Maybe<string>;
  is_odd_semester: boolean;
  subject: Subject;
  classroom: Classroom;
  studentassigments: StudentAssigment[];
  myanswer: Maybe<StudentAssigment>;
}

export interface StudentAssigment {
  id: string;
  created_at: string;
  updated_at: string;
  assigment: Assigment;
  user: User;
  attachments: Attachment[];
  content: Maybe<string>;
  external_url: Maybe<string>;
  grade: number;
  is_graded: boolean;
  comment: Maybe<string>;
  edited_times: number;
  turned_at: Maybe<string>;
}

export interface Absent {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  user: Maybe<User>;
  receiver: Maybe<User>;
  type: string;
  reason: string;
  finish_at: Maybe<string>;
  start_at: Maybe<string>;
}

export interface Article {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  user: User;
  slug: string;
  content: string;
  is_paid: boolean;
  role: string;
  visibility: Maybe<Visibility>;
  school: Maybe<School>;
  price: Maybe<Price>;
  thumbnail: Maybe<Attachment>;
  subjects: Subject[];
  classtypes: Classtype[];
  meetings: Meeting[];
  morphable: string;
  likeCount: number;
  commentCount: number;
  likes: Maybe<LikeConnection>;
  comments: Maybe<CommentConnection>;
}

export interface Price {
  priceable: Maybe<Priceable>;
}

export type Priceable = Article;
/** A paginated list of Like edges. */
export interface LikeConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Like edges.*/
  edges: Maybe<LikeEdge[]>;
}

/** Pagination information about the corresponding list of items. */
export interface PageInfo {
  /** When paginating forwards, are there more items?*/
  hasNextPage: boolean;
  /** When paginating backwards, are there more items?*/
  hasPreviousPage: boolean;
  /** When paginating backwards, the cursor to continue.*/
  startCursor: Maybe<string>;
  /** When paginating forwards, the cursor to continue.*/
  endCursor: Maybe<string>;
  /** Total number of node in connection.*/
  total: Maybe<number>;
  /** Count of nodes in current request.*/
  count: Maybe<number>;
  /** Current page of request.*/
  currentPage: Maybe<number>;
  /** Last page in connection.*/
  lastPage: Maybe<number>;
}

/** An edge that contains a node of type Like and a cursor. */
export interface LikeEdge {
  /** The Like node.*/
  node: Maybe<Like>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

export interface Like {
  id: string;
  created_at: string;
  updated_at: string;
  user: User;
}

/** A paginated list of Comment edges. */
export interface CommentConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Comment edges.*/
  edges: Maybe<CommentEdge[]>;
}

/** An edge that contains a node of type Comment and a cursor. */
export interface CommentEdge {
  /** The Comment node.*/
  node: Maybe<Comment>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

export interface Comment {
  id: string;
  created_at: string;
  updated_at: string;
  user: User;
  content: string;
}

export interface Room {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  messages: Message[];
  users: User[];
  roomable: Maybe<Roomable>;
  quizresults: Quizresult[];
  identifier: string;
  is_enabled: boolean;
}

export interface Message {
  id: string;
  created_at: string;
  updated_at: string;
  user: Maybe<User>;
  content: string;
  messageable: Maybe<Messageable>;
}

export type Messageable = Room | PrivateRoom;
export interface PrivateRoom {
  id: string;
  created_at: string;
  updated_at: string;
  messages: Message[];
  firstmessage: Maybe<Message>;
  first: User;
  second: User;
}

export type Roomable = Meeting | Quiz;
export interface Quiz {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  quizreward: Maybe<Quizreward>;
  questions: Question[];
  firstquestion: Question[];
  subject: Subject;
  user: User;
  rooms: Room[];
  thumbnail: Maybe<Attachment>;
  description: string;
  played_count: number;
  is_rewarded: boolean;
  difficulty: Maybe<QuizDifficulty>;
  visibility: Maybe<Visibility>;
  morphable: string;
  likeCount: number;
  commentCount: number;
  likes: Maybe<LikeConnection>;
  comments: Maybe<CommentConnection>;
}

export interface Quizreward {
  id: string;
  created_at: string;
  updated_at: string;
  transaction: Transaction;
  quiz: Quiz;
  reward: Reward;
}

export interface Transaction {
  id: string;
  created_at: string;
  updated_at: string;
  user: User;
  from: number;
  to: number;
  amount: number;
  voucher: Maybe<Voucher>;
  uuid: string;
  payment_method: Maybe<string>;
  status: Maybe<TransactionStatus>;
  is_paid: boolean;
  staging_url: Maybe<string>;
  invoice_request: Maybe<string>;
  invoice_response: Maybe<string>;
  transactionable: Maybe<Transactionable>;
  description: Maybe<string>;
}

export interface Voucher {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  code: string;
  percentage: number;
  description: Maybe<string>;
  expired_at: string;
}

export enum TransactionStatus {
  Staging = 'STAGING',
  Pending = 'PENDING',
  Success = 'SUCCESS',
  Failed = 'FAILED',
}
export type Transactionable = Access | Quizreward;
export interface Access {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  price: number;
  duration: number;
  roles: Maybe<Roles>;
  ability: Maybe<string[]>;
}

export enum Roles {
  Admin = 'ADMIN',
  Student_ppdb = 'STUDENT_PPDB',
  General = 'GENERAL',
  Teacher = 'TEACHER',
  Student = 'STUDENT',
  Bimbel = 'BIMBEL',
  Guardian = 'GUARDIAN',
}
export interface Reward {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  description: string;
  prize_pool: number;
  reward: number;
  minimum_play_count: number;
  is_active: Maybe<boolean>;
}

export enum QuizDifficulty {
  Easy = 'EASY',
  Medium = 'MEDIUM',
  Hard = 'HARD',
}
export interface Quizresult {
  id: string;
  created_at: string;
  updated_at: string;
  quiz: Quiz;
  user: User;
  grade: number;
  room: Maybe<Room>;
  start_at: string;
  finish_at: Maybe<string>;
}

export enum SubjectType {
  General = 'GENERAL',
  Vocational = 'VOCATIONAL',
  Local_content = 'LOCAL_CONTENT',
  Special_development = 'SPECIAL_DEVELOPMENT',
}
export interface FormTemplate {
  id: string;
  comment: Maybe<string>;
  data: FormTemplateData[];
  status: FormTemplateStatus;
  created_at: string;
  updated_at: string;
}

export interface FormTemplateData {
  hash: string;
  name: string;
  description: Maybe<string>;
  blocks: Input[];
}

export interface Input {
  hash: string;
  name: string;
  type: InputType;
  description: Maybe<string>;
  defaultValue: Maybe<string>;
  placeholder: Maybe<string>;
  required: Maybe<boolean>;
  regex: Maybe<string>;
  options: Maybe<string[]>;
  value: Maybe<string>;
  metadata: Maybe<any>;
}

export enum InputType {
  Select = 'SELECT',
  Text = 'TEXT',
  Number = 'NUMBER',
  Radio = 'RADIO',
  File = 'FILE',
  Date = 'DATE',
  Time = 'TIME',
  Textarea = 'TEXTAREA',
}

export enum FormTemplateStatus {
  Active = 'ACTIVE',
  Disabled = 'DISABLED',
}
export interface Extracurricular {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  abbreviation: string;
  description: string;
}

export interface Major {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  abbreviation: string;
  description: string;
}

export interface Wave {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  education_year_start: number;
  education_year_end: number;
  max_join: number;
  open_at: string;
  close_at: string;
  allow_extracurricular: boolean;
  allow_major: boolean;
  is_paid: boolean;
  price: number;
  school: School;
  studentppdbs: StudentPpdb[];
  studentppdbCount: number;
}

export interface StudentPpdb {
  id: string;
  created_at: string;
  updated_at: string;
  status: StudentPpdbStatus;
  comment: Maybe<string>;
  is_paid: boolean;
  changer: Maybe<User>;
  school: School;
  user: User;
  wave: Maybe<Wave>;
  form: Maybe<Form>;
  identifier: string;
  major: Maybe<Major>;
  extracurriculars: Extracurricular[];
}

export enum StudentPpdbStatus {
  Approved = 'APPROVED',
  Rejected = 'REJECTED',
  Processed = 'PROCESSED',
  Pending = 'PENDING',
  Permanent_rejected = 'PERMANENT_REJECTED',
}
/** A paginated list of Article edges. */
export interface ArticleConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Article edges.*/
  edges: Maybe<ArticleEdge[]>;
}

/** An edge that contains a node of type Article and a cursor. */
export interface ArticleEdge {
  /** The Article node.*/
  node: Maybe<Article>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** Dynamic WHERE conditions for the `where` argument on the query `attendances`. */
export interface SchoolAttendancesWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: SchoolAttendancesWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: SchoolAttendancesWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: SchoolAttendancesWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: SchoolAttendancesWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `attendances` on type `School`. */
export enum SchoolAttendancesWhereColumn {
  Id = 'ID',
}
/** The available SQL operators that are used to filter query results. */
export enum SQLOperator {
  Eq = 'EQ',
  Neq = 'NEQ',
  Gt = 'GT',
  Gte = 'GTE',
  Lt = 'LT',
  Lte = 'LTE',
  Like = 'LIKE',
  Not_like = 'NOT_LIKE',
  In = 'IN',
  Not_in = 'NOT_IN',
  Between = 'BETWEEN',
  Not_between = 'NOT_BETWEEN',
  Is_null = 'IS_NULL',
  Is_not_null = 'IS_NOT_NULL',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `attendances`. */
export interface SchoolAttendancesWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: SchoolAttendancesWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `hasUser` argument on the query `attendances`. */
export interface SchoolAttendancesHasUserWhereHasConditions {
  /** The column that is used for the condition.*/
  column?: SchoolAttendancesHasUserColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: SchoolAttendancesHasUserWhereHasConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: SchoolAttendancesHasUserWhereHasConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: SchoolAttendancesHasUserWhereHasConditionsRelation;
}

/** Allowed column names for the `hasUser` argument on field `attendances` on type `School`. */
export enum SchoolAttendancesHasUserColumn {
  Roles = 'ROLES',
}
/** Dynamic HAS conditions for WHERE conditions for the `hasUser` argument on the query `attendances`. */
export interface SchoolAttendancesHasUserWhereHasConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: SchoolAttendancesHasUserWhereHasConditions;
}

/** A paginated list of Attendance edges. */
export interface AttendanceConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Attendance edges.*/
  edges: Maybe<AttendanceEdge[]>;
}

/** An edge that contains a node of type Attendance and a cursor. */
export interface AttendanceEdge {
  /** The Attendance node.*/
  node: Maybe<Attendance>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of StudentPpdb edges. */
export interface StudentPpdbConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of StudentPpdb edges.*/
  edges: Maybe<StudentPpdbEdge[]>;
}

/** An edge that contains a node of type StudentPpdb and a cursor. */
export interface StudentPpdbEdge {
  /** The StudentPpdb node.*/
  node: Maybe<StudentPpdb>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** Dynamic WHERE conditions for the `where` argument on the query `consultations`. */
export interface UserConsultationsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserConsultationsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserConsultationsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserConsultationsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserConsultationsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `consultations` on type `User`. */
export enum UserConsultationsWhereColumn {
  Id = 'ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `consultations`. */
export interface UserConsultationsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserConsultationsWhereWhereConditions;
}

export interface Consultation {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  problem: string;
  notes: string;
  advice: string;
  user: Maybe<User>;
  consultant: Maybe<User>;
}

/** Dynamic WHERE conditions for the `where` argument on the query `adminschools`. */
export interface UserAdminschoolsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserAdminschoolsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserAdminschoolsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserAdminschoolsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserAdminschoolsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `adminschools` on type `User`. */
export enum UserAdminschoolsWhereColumn {
  School_id = 'SCHOOL_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `adminschools`. */
export interface UserAdminschoolsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserAdminschoolsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `homeroomschools`. */
export interface UserHomeroomschoolsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserHomeroomschoolsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserHomeroomschoolsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserHomeroomschoolsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserHomeroomschoolsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `homeroomschools` on type `User`. */
export enum UserHomeroomschoolsWhereColumn {
  School_id = 'SCHOOL_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `homeroomschools`. */
export interface UserHomeroomschoolsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserHomeroomschoolsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `headmasterschools`. */
export interface UserHeadmasterschoolsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserHeadmasterschoolsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserHeadmasterschoolsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserHeadmasterschoolsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserHeadmasterschoolsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `headmasterschools` on type `User`. */
export enum UserHeadmasterschoolsWhereColumn {
  School_id = 'SCHOOL_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `headmasterschools`. */
export interface UserHeadmasterschoolsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserHeadmasterschoolsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `absents`. */
export interface UserAbsentsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserAbsentsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserAbsentsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserAbsentsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserAbsentsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `absents` on type `User`. */
export enum UserAbsentsWhereColumn {
  Name = 'NAME',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `absents`. */
export interface UserAbsentsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserAbsentsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `subjects`. */
export interface UserSubjectsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserSubjectsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserSubjectsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserSubjectsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserSubjectsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `subjects` on type `User`. */
export enum UserSubjectsWhereColumn {
  Name = 'NAME',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `subjects`. */
export interface UserSubjectsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserSubjectsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `questions`. */
export interface UserQuestionsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserQuestionsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserQuestionsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserQuestionsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserQuestionsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `questions` on type `User`. */
export enum UserQuestionsWhereColumn {
  Subject_id = 'SUBJECT_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `questions`. */
export interface UserQuestionsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserQuestionsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `exams`. */
export interface UserExamsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserExamsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserExamsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserExamsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserExamsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `exams` on type `User`. */
export enum UserExamsWhereColumn {
  Name = 'NAME',
  Subject_id = 'SUBJECT_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `exams`. */
export interface UserExamsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserExamsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `classrooms`. */
export interface UserClassroomsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserClassroomsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserClassroomsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserClassroomsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserClassroomsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `classrooms` on type `User`. */
export enum UserClassroomsWhereColumn {
  Name = 'NAME',
  Subject_id = 'SUBJECT_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `classrooms`. */
export interface UserClassroomsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserClassroomsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `assigments`. */
export interface UserAssigmentsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserAssigmentsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserAssigmentsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserAssigmentsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserAssigmentsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `assigments` on type `User`. */
export enum UserAssigmentsWhereColumn {
  Id = 'ID',
  Name = 'NAME',
  Subject_id = 'SUBJECT_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `assigments`. */
export interface UserAssigmentsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserAssigmentsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `packagequestions`. */
export interface UserPackagequestionsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserPackagequestionsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserPackagequestionsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserPackagequestionsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserPackagequestionsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `packagequestions` on type `User`. */
export enum UserPackagequestionsWhereColumn {
  Name = 'NAME',
  Subject_id = 'SUBJECT_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `packagequestions`. */
export interface UserPackagequestionsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserPackagequestionsWhereWhereConditions;
}

export interface Packagequestion {
  id: string;
  name: string;
  created_at: string;
  updated_at: string;
  questions: Question[];
  user: User;
  subject: Subject;
  classtype: Classtype;
  editable: boolean;
  visibility: Maybe<Visibility>;
}

/** Dynamic WHERE conditions for the `where` argument on the query `meetings`. */
export interface UserMeetingsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserMeetingsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserMeetingsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserMeetingsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserMeetingsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `meetings` on type `User`. */
export enum UserMeetingsWhereColumn {
  Name = 'NAME',
  Subject_id = 'SUBJECT_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `meetings`. */
export interface UserMeetingsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserMeetingsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `examresults`. */
export interface UserExamresultsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserExamresultsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserExamresultsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserExamresultsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserExamresultsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `examresults` on type `User`. */
export enum UserExamresultsWhereColumn {
  Name = 'NAME',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `examresults`. */
export interface UserExamresultsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserExamresultsWhereWhereConditions;
}

/** Dynamic WHERE conditions for the `where` argument on the query `studentconsultations`. */
export interface UserStudentconsultationsWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserStudentconsultationsWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserStudentconsultationsWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserStudentconsultationsWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserStudentconsultationsWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `studentconsultations` on type `User`. */
export enum UserStudentconsultationsWhereColumn {
  Id = 'ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `studentconsultations`. */
export interface UserStudentconsultationsWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserStudentconsultationsWhereWhereConditions;
}

export interface Report {
  id: string;
  name: Maybe<string>;
  created_at: string;
  updated_at: string;
  data: Maybe<any>;
  user: User;
  users: User[];
  type: Maybe<ReportType>;
}

export enum ReportType {
  Grade = 'GRADE',
}
/** Dynamic WHERE conditions for the `where` argument on the query `quizzes`. */
export interface UserQuizzesWhereWhereConditions {
  /** The column that is used for the condition.*/
  column?: UserQuizzesWhereColumn;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: UserQuizzesWhereWhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: UserQuizzesWhereWhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: UserQuizzesWhereWhereConditionsRelation;
}

/** Allowed column names for the `where` argument on field `quizzes` on type `User`. */
export enum UserQuizzesWhereColumn {
  Name = 'NAME',
  Subject_id = 'SUBJECT_ID',
}
/** Dynamic HAS conditions for WHERE conditions for the `where` argument on the query `quizzes`. */
export interface UserQuizzesWhereWhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: UserQuizzesWhereWhereConditions;
}

export interface Identity {
  type: string;
  identifier: string;
}

export enum FormType {
  Request_tutor = 'REQUEST_TUTOR',
  Request_counselor = 'REQUEST_COUNSELOR',
  Request_headmaster = 'REQUEST_HEADMASTER',
  Request_admin_school = 'REQUEST_ADMIN_SCHOOL',
  Request_homeroom = 'REQUEST_HOMEROOM',
  Request_student_ppdb = 'REQUEST_STUDENT_PPDB',
}
export enum FormStatus {
  Pending = 'PENDING',
  Processed = 'PROCESSED',
  Finished = 'FINISHED',
  Rejected = 'REJECTED',
}
/** A paginated list of Attachment edges. */
export interface AttachmentConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Attachment edges.*/
  edges: Maybe<AttachmentEdge[]>;
}

/** An edge that contains a node of type Attachment and a cursor. */
export interface AttachmentEdge {
  /** The Attachment node.*/
  node: Maybe<Attachment>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of Form edges. */
export interface FormConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Form edges.*/
  edges: Maybe<FormEdge[]>;
}

/** An edge that contains a node of type Form and a cursor. */
export interface FormEdge {
  /** The Form node.*/
  node: Maybe<Form>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of Province edges. */
export interface ProvinceConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Province edges.*/
  edges: Maybe<ProvinceEdge[]>;
}

/** An edge that contains a node of type Province and a cursor. */
export interface ProvinceEdge {
  /** The Province node.*/
  node: Maybe<Province>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of City edges. */
export interface CityConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of City edges.*/
  edges: Maybe<CityEdge[]>;
}

/** An edge that contains a node of type City and a cursor. */
export interface CityEdge {
  /** The City node.*/
  node: Maybe<City>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of District edges. */
export interface DistrictConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of District edges.*/
  edges: Maybe<DistrictEdge[]>;
}

/** An edge that contains a node of type District and a cursor. */
export interface DistrictEdge {
  /** The District node.*/
  node: Maybe<District>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of User edges. */
export interface UserConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of User edges.*/
  edges: Maybe<UserEdge[]>;
}

/** An edge that contains a node of type User and a cursor. */
export interface UserEdge {
  /** The User node.*/
  node: Maybe<User>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of School edges. */
export interface SchoolConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of School edges.*/
  edges: Maybe<SchoolEdge[]>;
}

/** An edge that contains a node of type School and a cursor. */
export interface SchoolEdge {
  /** The School node.*/
  node: Maybe<School>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of Transaction edges. */
export interface TransactionConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Transaction edges.*/
  edges: Maybe<TransactionEdge[]>;
}

/** An edge that contains a node of type Transaction and a cursor. */
export interface TransactionEdge {
  /** The Transaction node.*/
  node: Maybe<Transaction>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of Quiz edges. */
export interface QuizConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Quiz edges.*/
  edges: Maybe<QuizEdge[]>;
}

/** An edge that contains a node of type Quiz and a cursor. */
export interface QuizEdge {
  /** The Quiz node.*/
  node: Maybe<Quiz>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of Question edges. */
export interface QuestionConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Question edges.*/
  edges: Maybe<QuestionEdge[]>;
}

/** An edge that contains a node of type Question and a cursor. */
export interface QuestionEdge {
  /** The Question node.*/
  node: Maybe<Question>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of Packagequestion edges. */
export interface PackagequestionConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Packagequestion edges.*/
  edges: Maybe<PackagequestionEdge[]>;
}

/** An edge that contains a node of type Packagequestion and a cursor. */
export interface PackagequestionEdge {
  /** The Packagequestion node.*/
  node: Maybe<Packagequestion>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

/** A paginated list of Subject edges. */
export interface SubjectConnection {
  /** Pagination information about the list of edges.*/
  pageInfo: PageInfo;
  /** A list of Subject edges.*/
  edges: Maybe<SubjectEdge[]>;
}

/** An edge that contains a node of type Subject and a cursor. */
export interface SubjectEdge {
  /** The Subject node.*/
  node: Maybe<Subject>;
  /** A unique cursor that can be used for pagination.*/
  cursor: string;
}

export type Agendaable = Meeting | Exam;
export interface Examtracker {
  id: string;
  created_at: string;
  updated_at: string;
  examsession: Examsession;
  exam: Exam;
  user: User;
  last_activity: string;
  minute_passed: number;
}

export enum Gender {
  Male = 'Male',
  Female = 'Female',
}
export interface FormData {
  message: Maybe<string>;
}

export type FormTemplateDataInput = FormTemplateData | FormData;
/** The available directions for ordering a list of records. */
export enum SortOrder {
  Asc = 'ASC',
  Desc = 'DESC',
}
/** Allows ordering a list of records. */
export interface OrderByClause {
  /** The column that is used for ordering.*/
  column: string;
  /** The direction that is used for ordering.*/
  order: SortOrder;
}

/** Pagination information about the corresponding list of items. */
export interface PaginatorInfo {
  /** Count of available items in the page.*/
  count: number;
  /** Current pagination page.*/
  currentPage: number;
  /** Index of first item in the current page.*/
  firstItem: Maybe<number>;
  /** If collection has more pages.*/
  hasMorePages: boolean;
  /** Index of last item in the current page.*/
  lastItem: Maybe<number>;
  /** Last page number of the collection.*/
  lastPage: number;
  /** Number of items per page in the collection.*/
  perPage: number;
  /** Total items available in the collection.*/
  total: number;
}

/** Pagination information about the corresponding list of items. */
export interface SimplePaginatorInfo {
  /** Count of available items in the page.*/
  count: number;
  /** Current pagination page.*/
  currentPage: number;
  /** Index of first item in the current page.*/
  firstItem: Maybe<number>;
  /** Index of last item in the current page.*/
  lastItem: Maybe<number>;
  /** Number of items per page in the collection.*/
  perPage: number;
}

/** Specify if you want to include or exclude trashed results from a query. */
export enum Trashed {
  Only = 'ONLY',
  With = 'WITH',
  Without = 'WITHOUT',
}
/** Dynamic WHERE conditions for queries. */
export interface WhereConditions {
  /** The column that is used for the condition.*/
  column?: string;
  /** @default EQThe operator that is used for the condition.*/
  operator?: SQLOperator;
  /** The value that is used for the condition.*/
  value?: any;
  /** A set of conditions that requires all conditions to match.*/
  AND?: WhereConditions[];
  /** A set of conditions that requires at least one condition to match.*/
  OR?: WhereConditions[];
  /** Check whether a relation exists. Extra conditions or a minimum amount can be applied.*/
  HAS?: WhereConditionsRelation;
}

/** Dynamic HAS conditions for WHERE condition queries. */
export interface WhereConditionsRelation {
  /** The relation that is checked.*/
  relation: string;
  /** @default GTEThe comparison operator to test against the amount.*/
  operator?: SQLOperator;
  /** @default 1The amount to test.*/
  amount?: number;
  /** Additional condition logic.*/
  condition?: WhereConditions;
}

export interface formArgs {
  id: string;
}

export interface provincesArgs {}

export interface citiesArgs {}

export interface districtsArgs {
  city_id?: string;
}

export interface meArgs {}

export interface quizArgs {
  id: string;
}

export interface userArgs {
  id: string;
}

export interface schoolArgs {
  id: string;
}

export interface ppdbschoolsArgs {
  schooltype_id?: string;
  city_id?: string;
  province_id?: string;
  district_id?: string;
  name?: string;
}

export interface reportArgs {
  id: string;
}

export interface consultationArgs {
  id: string;
}

export interface attendanceArgs {
  id: string;
}

export interface assigmentArgs {
  id: string;
}

export interface transactionArgs {
  id: string;
}

export interface accessesArgs {
  roles?: Roles;
}

export interface examtypesArgs {}

export interface subjectsArgs {}

export interface classtypesArgs {}

export interface schooltypesArgs {}

export interface articleArgs {
  slug: string;
}

export interface studentppdbArgs {
  id: string;
}

export interface attachmentsArgs {
  where?: QueryAttachmentsWhereWhereConditions;
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface formsArgs {
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface schoolformsArgs {
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface provincesAdminArgs {
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface citiesAdminArgs {
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface districtsAdminArgs {
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface usersArgs {
  district_id?: string;
  city_id?: string;
  roles?: Roles;
  nisn?: string;
  is_bimbel?: boolean;
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface schoolsArgs {
  where?: QuerySchoolsWhereWhereConditions;
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface transactionsArgs {
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface quizzesArgs {
  where?: QueryQuizzesWhereWhereConditions;
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface questionsArgs {
  subject_id?: string;
  classtype_id?: string;
  where?: QueryQuestionsWhereWhereConditions;
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface packagequestionsArgs {
  subject_id?: string;
  classtype_id?: string;
  where?: QueryPackagequestionsWhereWhereConditions;
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}

export interface subjectsAdminArgs {
  /** Limits number of fetched elements.*/
  first: number;
  /** A cursor after which elements are returned.*/
  after?: string;
}
