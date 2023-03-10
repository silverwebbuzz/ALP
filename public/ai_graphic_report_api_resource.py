# -*- coding: utf-8 -*-
"""AI Graphic Report API Resource.ipynb

Automatically generated by Colaboratory.

Original file is located at
    https://colab.research.google.com/drive/1UTkMiPUi7lGjL7QNcSctl1S4N_MAAe3k
"""

from collections import defaultdict
# import requests
import pandas as pd
# import json
# from google.colab import files

import numpy as np
from scipy import stats
import matplotlib.pyplot as plt
import matplotlib.pylab as pylab
params = {'legend.fontsize': 'x-large',
          'figure.figsize': (15, 5),
         'axes.labelsize': 'x-large',
         'axes.titlesize':'x-large',
         'xtick.labelsize':'x-large',
         'ytick.labelsize':'x-large'}
pylab.rcParams.update(params)
from scipy.stats import norm, skewnorm
import random

import math
def normalize(x):
    return math.exp(x)/(1 + math.exp(x))

def denormalize(y):
    return math.log(y)-math.log(1-y)

def denormalize_np(y):
    return np.log(y)- np.log(1-y)

def gen_data(n, a):
    a, loc, scale = a, 0, 1

    # draw n skew normal samples
    data = stats.skewnorm(a, loc, scale).rvs(n)
    
    # estimate parameters from samples
    ae, loce, scalee = stats.skewnorm.fit(data)
    mean, var, skew, kurt = skewnorm.stats(ae, moments='mvsk')
    median = stats.skewnorm.ppf(0.5, ae, loce, scalee)
    adjusted_mean = mean - median
    
    
    # Adjust to data2 by median to make median2 = 0
    data2 = data - median*np.ones(np.shape(data))
    sample_mean = data2.mean()
    sample_var = data2.var()
    
    # Plot the historgram of Student Competence
    fig, ax = plt.subplots(1, 1, figsize=(12, 7), facecolor='lightgray')
    twin1 = ax.twinx()
    # twin2 = ax.twinx()
    ax.set_title("Student Competence Population", fontsize = 15)
    _ = twin1.hist(data2, bins=np.ceil(n/10).astype(int), density = False, alpha=0.6, color='darkgreen')
    
    # Plot the Probabiltity Density Function
    xmin, xmax = plt.xlim()

    if 0 - xmin >= xmax - 0:  
        ax.text(0.15, 0.2, 'Mean\nVar\nSkew\nkurt', transform = fig.transFigure) 
        ax.text(0.2, 0.2, '=\n=\n=\n=' , transform = fig.transFigure) 
        ax.text(0.25, 0.2,  '%5.3f\n%5.3f\n%5.3f\n%5.3f' % (sample_mean, sample_var, skew, kurt), transform = fig.transFigure, ha ='right')
    else:
        ax.text(1-0.25, 0.2, 'Mean\nVar\nSkew\nkurt', transform = fig.transFigure) 
        ax.text(1-0.2, 0.2, '=\n=\n=\n=' , transform = fig.transFigure)  
        ax.text(1-0.15, 0.2,  '%5.3f\n%5.3f\n%5.3f\n%5.3f' % (sample_mean, sample_var, skew, kurt), transform = fig.transFigure, ha='right')


    x = np.linspace(xmin, xmax, 100)
    p = stats.skewnorm.pdf(x + median ,ae, loce, scalee) #.rvs(n/10)
    ax.plot(x, p, color = 'navy', linewidth=0.5)

    # ax.text(-2.7, 0.62, 'Mean \nVar \nSkew \nkurt')  
    # ax.text(-2.6, 0.62,  '= %5.3f \n= %5.3f \n= %5.3f \n= %5.3f' % (sample_mean, sample_var, skew, kurt))


 
    # Plot the Cumulative Density
    # cdf = stats.skewnorm.cdf(x + median, ae, loce, scalee)
    # twin2.plot(x, cdf, color = 'firebrick', linewidth=0.5)
    # twin2.axvline(x = 0, linewidth=1)
    # twin2.axhline(y=0.5, linewidth=1)

    # Labels and spines

    ax.set_ylim(0, top=None)
    ax.set_xlabel('Competence')
    ax.set_ylabel('Population Density', color = 'navy')
    ax.axvline(x = 0, linewidth=1)

    twin1.set_ylim(0, top=None)
    twin1.set_ylabel('No. of Students', color = 'darkgreen')
    # twin1.spines['right'].set_position(("axes", 1.08))
    twin1.set_yticks(np.linspace(0, np.ceil(twin1.get_ybound()[1])),11)

    # twin2.set_ylim(0, 1)
    # twin2.set_ylabel('Cumulative Density', color = 'firebrick')
    # twin2.set_yticks(np.linspace(0, (np.ceil(twin2.get_ybound()[1]*10))/10, 11))

    plt.show()

    print(adjusted_mean, var, skew, kurt)
    adjusted_sample_mean = np.mean(data2) 
    adjusted_sample_median = np.median(data2)
    sample_var = np.var(data2)
    print(adjusted_sample_median, adjusted_sample_mean, sample_var)

    return sample_mean, sample_var, skew.flatten(), kurt.flatten(), data2

mean, var, skew, kurt, data = gen_data(50, -4)

data_list = data.tolist()

data_list

def analyze_data(data_list, plot_title, left_y_axis, right_y_axis, x_axis):

    n = len(data_list)
    data = np.array(data_list)
    mean = data.mean()
    median = np.median(data)
    var = data.var()
    std = data.std()

    ae, loce, scalee = stats.skewnorm.fit(data)

    _, _, skew, kurt = skewnorm.stats(ae, moments='mvsk')
    #skew = skew[0]
    #kurt = kurt[0]


    # Plot the historgram of Student Competence
    fig, ax = plt.subplots(1, 1, figsize=(18, 10), facecolor='lightgray')
    twin1 = ax.twinx()
    # twin2 = ax.twinx()
    ax.set_title(plot_title, fontsize = 15)
    _ = twin1.hist(data, bins=np.ceil(n/10).astype(int), density = False, alpha=0.6, color='darkgreen')
    # _ = twin1.hist(data, bins=np.ceil(n/2).astype(int), density = False, alpha=0.6, color='darkgreen')
    # Plot the Probabiltity Density Function
    xmin, xmax = plt.xlim()   

    if 0 - xmin >= xmax - 0:  
        ax.text(0.15, 0.2, 'Mean\nMedian\nStd\nSkew\nkurt', transform = fig.transFigure) 
        ax.text(0.2, 0.2, '=\n=\n=\n=\n=' , transform = fig.transFigure) 
        ax.text(0.25, 0.2,  '%5.3f\n%5.3f\n%5.3f\n%5.3f\n%5.3f' % (mean, median, std, skew, kurt), transform = fig.transFigure, ha ='right')
    else:
        ax.text(1-0.25, 0.2, 'Mean\nMedian\nStd\nSkew\nkurt', transform = fig.transFigure) 
        ax.text(1-0.2, 0.2, '=\n=\n=\n=\n=' , transform = fig.transFigure)  
        ax.text(1-0.15, 0.2,  '%5.3f\n%5.3f\n%5.3f\n%5.3f\n%5.3f' % (mean, median, std, skew, kurt), transform = fig.transFigure, ha='right')


    x = np.linspace(xmin, xmax, 100)
    # p = stats.skewnorm.pdf(x + median ,ae, loce, scalee) #.rvs(n/10)
    p = stats.skewnorm.pdf(x ,ae, loce, scalee) #.rvs(n/10)
    ax.plot(x, p, color = 'navy', linewidth=0.5) 

    # Plot the Cumulative Density
    # cdf = stats.skewnorm.cdf(x + median, ae, loce, scalee)
    # twin2.plot(x, cdf, color = 'firebrick', linewidth=0.5)
    # twin2.axvline(x = 0, linewidth=1)
    # twin2.axhline(y=0.5, linewidth=1)

    # Labels and spines

    ax.set_ylim(0, top=None)
    ax.set_xlabel(x_axis)
    ax.set_ylabel(left_y_axis, color = 'navy')
    ax.axvline(x = median, linewidth=1)

    twin1.set_ylim(0, top=None)
    twin1.set_ylabel(right_y_axis, color = 'darkgreen')
   
    # twin1.set_yticks(np.linspace(0, np.ceil(twin1.get_ybound()[1])),11)
    # cdf = stats.skewnorm.cdf(x + median, ae, loce, scalee)
    # twin2.plot(x, cdf, color = 'firebrick', linewidth=0.5)
    # twin2.axvline(x = 0, linewidth=1)
    # twin2.axhline(y=0.5, linewidth=1)inspace(0, np.ceil(twin1.get_ybound()[1])),11)

    # twin2.set_ylim(0, 1)
    # twin2.set_ylabel('Cumulative Density', color = 'firebrick')
    # twin2.set_yticks(np.linspace(0, (np.ceil(twin2.get_ybound()[1]*10))/10, 11))
    # twin1.legend(['Mean'])

    plt.show()
    # plt.savefig('plots/' + plot_title +'.png')

plot_title = "Title"
left_y_axis = "Data Density"
right_y_axis = "Occurence"
x_axis = "Value"

analyze_data(data_list, plot_title, left_y_axis, right_y_axis, x_axis)

data_list

plot_title

def analyze_question(question_difficulty, question_results_list, student_abilities_list):
    n = len(question_results_list)
    
    question_results = np.array(question_results_list)
    student_abilities = np.array(student_abilities_list)
    
    correct_students = []
    incorrect_students =[]
    for i in range(n):
        if question_results[i]:
            correct_students.append([i, student_abilities[i]])
        else:
            incorrect_students.append([i, student_abilities[i]])

    correct_student_abilities = [item[1] for item in correct_students]
    incorrect_student_abilities = [item[1] for item in incorrect_students]

    correct_num = len(correct_student_abilities)
    incorrect_num = len(incorrect_student_abilities)

    incorrect_percent = incorrect_num/(incorrect_num + correct_num)
    correct_percent = 1 - incorrect_percent

    normalized_difficulty = math.exp(question_difficulty)/(1 + math.exp(question_difficulty))*100
    
    fig, ax = plt.subplots(1, 1, figsize=(12, 7), facecolor='lightgray')
    # twin1 = ax.twinx()
    # twin2 = ax.twinx()

    ax.set_title("Students' Performances at this Question", fontsize=15)
    twin1 = ax.twinx()

    _= twin1.hist(incorrect_student_abilities, bins=np.ceil(n/10).astype(int), density = False, alpha=0.2, color='red')

    _= twin1.hist(correct_student_abilities, bins=np.ceil(n/10).astype(int), density = False, alpha=0.2, color='green')
    # ax.set_ylim(0, 0.4)

    ae, loce, scalee = stats.skewnorm.fit(incorrect_student_abilities)
    ae1, loce1, scalee1 = stats.skewnorm.fit(correct_student_abilities)

    xmin = min(min(incorrect_student_abilities), min(correct_student_abilities)) 
    xmax = max(max(incorrect_student_abilities), max(correct_student_abilities))

    #xmax = plt.xlim()
    x = np.linspace(xmin, xmax, 100)
    q = stats.skewnorm.pdf(x ,ae, loce, scalee)*incorrect_num/(incorrect_num + correct_num)
    p = stats.skewnorm.pdf(x ,ae1, loce1, scalee1)*correct_num/(incorrect_num + correct_num)

    
    ax.plot(x, q, color = 'red', alpha=1, linewidth=0.5)
    ax.fill_between(x, q, color = 'red', alpha=0.15, linewidth=0.5, label="Incorrect Students : {:4.1f}%".format(incorrect_percent*100))
    ax.plot(x, p, color = 'green', alpha=1, linewidth=0.5)
    ax.fill_between(x, p, color = 'green', alpha=0.2, linewidth=0.5, label = "Correct Students : {:4.1f}%".format(correct_percent*100))

    ax.axvline(x = question_difficulty, color = 'red', linewidth=1, alpha=1, label="Question Normalized Difficulty : {:4.1f}%".format(normalized_difficulty))
    # ax.axvline(x = difficulty[0] +0.25, color = 'grey', linewidth=0.8, alpha=1, label="Apparent Difficulty = {:5.3f}%".format(apparent_difficulty))
    # ax.axvline(x = apparent_difficulty, color = 'black', linewidth=0.5, alpha=1, label="Observed Difficulty = {:5.3f}".format(observed_difficulty))

    # ax.set_ylim(0, top=None)
    ax.set_xlabel('Student Ability')
    ax.set_ylabel('Population Density', color = 'black')

    twin1.set_ylabel('No. of Students', color = 'black')
    # twin1.set_yticks(np.linspace(0, (np.ceil(twin1.get_ybound()[1]*10))/10, 11))
    # twin2.set_yticks(np.linspace(0, (np.ceil(twin2.get_ybound()[1]*10))/10, 11))


    # twin1.set_yticks(np.linspace(0, (np.ceil(twin1.get_ybound()[1]*10))/10, 11))

    ax.set_ylim(0, ax.get_ylim()[1])

    # y_lims = ax.get_ylim()
    # twin2.set_ylim(0, 1)

   
    ax.legend(fancybox=True, shadow=True)

student_mean, student_var, student_skew, student_kurt, student_abilities = gen_data(1000, -4)

question_mean, question_var, question_skew, question_kurt, question_difficulties = gen_data(500, 5)

def answer_question (student, question, choice_num):
    results = [True, False]
    win_probabilty = norm.cdf(student - question)
    correct_answer = win_probabilty + (1 - win_probabilty)/choice_num
    return random.choices(results, weights = [correct_answer, 1-correct_answer], k = 1)[0]

def get_questions_results (students, questions):
    m = len(students)
    n = len(questions)
    questions_results = np.empty((m,n), dtype = bool)
    for i in range(m):
        for j in range(n):
            questions_results[i,j]=answer_question(students[i], questions[j], 4)
    return questions_results

questions_results = get_questions_results(student_abilities, question_difficulties)

question_results_list = questions_results[:, 25].tolist()

student_abilities_list = student_abilities.tolist()

question_difficulties

question_results_list

student_abilities_list

analyze_question(question_difficulties[25], question_results_list, student_abilities_list)

student_ability = student_abilities[123]

student_results_list = questions_results[123].tolist()

questions_difficulties_list = question_difficulties.tolist()

student_ability

student_results_list

questions_difficulties_list

def analyze_student(student_ability, student_results_list, questions_difficulties_list):

    n = len(student_results_list)
    student_results = np.array(student_results_list)
    questions_difficulties = np.array(questions_difficulties_list)

    correct_questions = []
    incorrect_questions = []

    for i in range(len(student_results)):
        if student_results[i]:
            correct_questions.append([i, questions_difficulties[i]])
        else:
            incorrect_questions.append([i, questions_difficulties[i]])

    incorrect_questions_difficulties = [item[1] for item in incorrect_questions]
    correct_questions_difficulties = [item[1] for item in correct_questions]

    incorrect_num = len(incorrect_questions_difficulties)
    correct_num = len(correct_questions_difficulties)

    incorrect_percent = incorrect_num/(incorrect_num + correct_num)
    correct_percent = 1 - incorrect_percent

    normalized_student_ability = math.exp(student_ability)/(1 + math.exp(student_ability))*100

    fig, ax = plt.subplots(1, 1, figsize=(12, 7), facecolor='lightgray')

    ax.set_title("Student Performance", fontsize=15)
    twin1 = ax.twinx()

    _= twin1.hist(incorrect_questions_difficulties, bins=np.ceil(n/10).astype(int), density = False, alpha=0.2, color='red')

    _= twin1.hist(correct_questions_difficulties, bins=np.ceil(n/10).astype(int), density = False, alpha=0.2, color='green')


    ae, loce, scalee = stats.skewnorm.fit(incorrect_questions_difficulties)
    ae1, loce1, scalee1 = stats.skewnorm.fit(correct_questions_difficulties)

    xmin = min(min(incorrect_questions_difficulties), min(correct_questions_difficulties)) 
    xmax = max(max(incorrect_questions_difficulties), max(correct_questions_difficulties))

    x = np.linspace(xmin, xmax, 100)
    q = stats.skewnorm.pdf(x ,ae, loce, scalee)*incorrect_num/(incorrect_num + correct_num)
    p = stats.skewnorm.pdf(x ,ae1, loce1, scalee1)*correct_num/(incorrect_num + correct_num)

    ax.plot(x, p, color = 'green', alpha=1, linewidth=0.5)
    ax.fill_between(x, p, color = 'green', alpha=0.2, linewidth=0.5, label = "Correct Questions : {:4.1f}%".format(correct_percent*100))
    ax.plot(x, q, color = 'red', alpha=1, linewidth=0.5)
    ax.fill_between(x, q, color = 'red', alpha=0.2, linewidth=0.5, label="Incorrect Questions : {:4.1f}%".format(incorrect_percent*100))

    ax.axvline(x = student_ability, color = 'red', linewidth=1, alpha=1, label="Student Normalized Ability : {:4.1f}%".format(normalized_student_ability))

    ax.set_xlabel('Question Difficulty')
    ax.set_ylabel('Question Population Density', color = 'black')

    twin1.set_ylabel('No. of Questions', color = 'black')

    ax.set_ylim(0, ax.get_ylim()[1])
    twin1.set_ylim(0, twin1.get_ylim()[1])

    ax.legend(fancybox=True, shadow=True)

analyze_student(student_ability, student_results_list, questions_difficulties_list)